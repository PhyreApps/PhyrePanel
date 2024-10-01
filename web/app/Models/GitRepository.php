<?php

namespace App\Models;

use App\GitClient;
use App\Models\Scopes\CustomerDomainScope;
use App\Models\Scopes\CustomerHostingSubscriptionScope;
use App\ShellApi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Psy\sh;

class GitRepository extends Model
{
    use HasFactory;

    public $timestamps = true;

    const STATUS_PENDING = 'pending';

    const STATUS_CLONING = 'cloning';
    const STATUS_CLONED = 'cloned';
    const STATUS_FAILED = 'failed';

    const STATUS_PULLING = 'pulling';

    const STATUS_UP_TO_DATE = 'up_to_date';

    protected $fillable = [
        'name',
        'url',
        'branch',
        'tag',
        'clone_from',
        'last_commit_hash',
        'last_commit_message',
        'last_commit_date',
        'status',
        'status_message',
        'dir',
        'domain_id',
        'git_ssh_key_id',
        'deployment_script',
        'quick_deploy'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new CustomerDomainScope());
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->clone();
        });

        static::deleting(function ($model) {
            $projectDir = $model->domain->domain_root . '/' . $model->dir;
            ShellApi::safeDelete($projectDir,[
                $model->domain->domain_root . '/',
            ]);
        });
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    private function _getSSHKey($gitSshKeyId, $findHostingSubscription)
    {
        $gitSSHKey = GitSshKey::where('id', $gitSshKeyId)
            ->where('hosting_subscription_id', $findHostingSubscription->id)
            ->first();

        if ($gitSSHKey) {
            $sshPath = '/home/'.$findHostingSubscription->system_username .'/.ssh';
            $privateKeyFile = $sshPath.'/id_rsa_'. $gitSSHKey->id;
            $publicKeyFile = $sshPath.'/id_rsa_'.$gitSSHKey->id.'.pub';

            if (!is_dir($sshPath)) {
                shell_exec('mkdir -p ' . $sshPath);
            }

            shell_exec('chown '.$findHostingSubscription->system_username.':'.$findHostingSubscription->system_username.' -R ' . $sshPath);
            shell_exec('chmod 0700 ' . $sshPath);

            if (!file_exists($privateKeyFile)) {
                file_put_contents($privateKeyFile, $gitSSHKey->private_key);
            }

            shell_exec('chown '.$findHostingSubscription->system_username.':'.$findHostingSubscription->system_username.' ' . $privateKeyFile);
            shell_exec('chmod 0400 ' . $privateKeyFile);

            if (!file_exists($publicKeyFile)) {
                file_put_contents($publicKeyFile, $gitSSHKey->public_key);
            }

            shell_exec('chown '.$findHostingSubscription->system_username.':'.$findHostingSubscription->system_username.' ' . $publicKeyFile);
            shell_exec('chmod 0400 ' . $publicKeyFile);


            return [
                'privateKeyFile' => $privateKeyFile,
                'publicKeyFile' => $publicKeyFile,
            ];
        }
    }

    public function pull()
    {
        $this->status = self::STATUS_PULLING;
        $this->save();

        $findDomain = Domain::find($this->domain_id);
        if (!$findDomain) {
            $this->status = self::STATUS_FAILED;
            $this->status_message = 'Domain not found';
            $this->save();
            return;
        }

        $findHostingSubscription = HostingSubscription::find($findDomain->hosting_subscription_id);
        if (!$findHostingSubscription) {
            $this->status = self::STATUS_FAILED;
            $this->status_message = 'Hosting Subscription not found';
            $this->save();
            return;
        }

        $projectDir = $findDomain->domain_root . '/' . $this->dir;

        $privateKeyFile = null;
        $getSSHKey = $this->_getSSHKey($this->git_ssh_key_id, $findHostingSubscription);
        if (isset($getSSHKey['privateKeyFile'])) {
            $privateKeyFile = $getSSHKey['privateKeyFile'];
        }

        $gitSSHUrl = GitClient::parseGitUrl($this->url);
        if (!isset($gitSSHUrl['provider'])) {
            $this->status = self::STATUS_FAILED;
            $this->status_message = 'Provider not found';
            $this->save();
            return;
        }

        $cloneUrl = 'git@'.$gitSSHUrl['provider'].':'.$gitSSHUrl['owner'].'/'.$gitSSHUrl['name'].'.git';

        $shellFile = $findDomain->domain_root . '/git/tmp/git-pull-' . $this->id . '.sh';
        $shellLog = $findDomain->domain_root . '/git/tmp/git-action-' . $this->id . '.log';

        shell_exec('mkdir -p ' . dirname($shellFile));
        shell_exec('chown '.$findHostingSubscription->system_username.':'.$findHostingSubscription->system_username.' -R ' . dirname(dirname($shellFile)));

        $shellContent = view('actions.git.pull-repo-user', [
            'gitProvider' => $gitSSHUrl['provider'],
            'systemUsername' => $findHostingSubscription->system_username,
            'gitRepositoryId' => $this->id,
            'cloneUrl' => $cloneUrl,
            'projectDir' => $projectDir,
            'privateKeyFile' => $privateKeyFile,
            'selfFile' => $shellFile,
            'deploymentScript'=>$this->deployment_script
        ])->render();

        file_put_contents($shellFile, $shellContent);


        $gitExecutorTempPath = storage_path('app/git/tmp');
        shell_exec('mkdir -p ' . $gitExecutorTempPath);

        $gitExecutorShellFile = $gitExecutorTempPath . '/git-pull-' . $this->id . '.sh';
        $gitExecutorShellFileLog = $gitExecutorTempPath . '/git-pull-' . $this->id . '.log';

        $gitExecutorContent = view('actions.git.git-executor', [
            'gitProvider' => $gitSSHUrl['provider'],
            'shellFile' => $shellFile,
            'shellLog' => $shellLog,
            'systemUsername' => $findHostingSubscription->system_username,
            'selfFile' => $gitExecutorShellFile,
            'afterCommand' => 'phyre-php /usr/local/phyre/web/artisan git-repository:mark-as-pulled '.$this->id,
        ])->render();

        file_put_contents($gitExecutorShellFile, $gitExecutorContent);

        shell_exec('chmod +x ' . $gitExecutorShellFile);
        shell_exec('bash ' . $gitExecutorShellFile . ' >> ' . $gitExecutorShellFileLog . ' &');

    }

    public function clone()
    {
        $this->status = self::STATUS_CLONING;
        $this->save();

        $findDomain = Domain::find($this->domain_id);
        if (!$findDomain) {
            $this->status = self::STATUS_FAILED;
            $this->status_message = 'Domain not found';
            $this->save();
            return;
        }

        $findHostingSubscription = HostingSubscription::find($findDomain->hosting_subscription_id);
        if (!$findHostingSubscription) {
            $this->status = self::STATUS_FAILED;
            $this->status_message = 'Hosting Subscription not found';
            $this->save();
            return;
        }

        $projectDir = $findDomain->domain_root . '/' . $this->dir;

        $privateKeyFile = null;
        $getSSHKey = $this->_getSSHKey($this->git_ssh_key_id, $findHostingSubscription);
        if (isset($getSSHKey['privateKeyFile'])) {
            $privateKeyFile = $getSSHKey['privateKeyFile'];
        }


        $gitSSHUrl = GitClient::parseGitUrl($this->url);
        if (!isset($gitSSHUrl['provider'])) {
            $this->status = self::STATUS_FAILED;
            $this->status_message = 'Provider not found';
            $this->save();
            return;
        }

        if ($privateKeyFile) {
            $cloneUrl = 'git@'.$gitSSHUrl['provider'].':'.$gitSSHUrl['owner']
                .'/'.$gitSSHUrl['name'].'.git';
        } else {
            $cloneUrl = 'https://'.$gitSSHUrl['provider'].'/'.$gitSSHUrl['owner']
                .'/'.$gitSSHUrl['name'].'.git';
        }

        $shellFile = $findDomain->domain_root . '/git/tmp/git-clone-' . $this->id . '.sh';
        $shellLog = $findDomain->domain_root . '/git/tmp/git-action-' . $this->id . '.log';

        shell_exec('mkdir -p ' . dirname($shellFile));
        shell_exec('chown '.$findHostingSubscription->system_username.':'.$findHostingSubscription->system_username.' -R ' . dirname(dirname($shellFile)));

        $shellContent = view('actions.git.clone-repo-user', [
            'gitProvider' => $gitSSHUrl['provider'],
            'systemUsername' => $findHostingSubscription->system_username,
            'gitRepositoryId' => $this->id,
            'cloneUrl' => $cloneUrl,
            'projectDir' => $projectDir,
            'privateKeyFile' => $privateKeyFile,
            'selfFile' => $shellFile,
        ])->render();

        file_put_contents($shellFile, $shellContent);


        $gitExecutorTempPath = storage_path('app/git/tmp');
        shell_exec('mkdir -p ' . $gitExecutorTempPath);

        $gitExecutorShellFile = $gitExecutorTempPath . '/git-clone-' . $this->id . '.sh';
        $gitExecutorShellFileLog = $gitExecutorTempPath . '/git-clone-' . $this->id . '.log';

        $gitExecutorContent = view('actions.git.git-executor', [
            'gitProvider' => $gitSSHUrl['provider'],
            'shellFile' => $shellFile,
            'shellLog' => $shellLog,
            'systemUsername' => $findHostingSubscription->system_username,
            'selfFile' => $gitExecutorShellFile,
            'afterCommand' => 'phyre-php /usr/local/phyre/web/artisan git-repository:mark-as-cloned '.$this->id,
        ])->render();

        file_put_contents($gitExecutorShellFile, $gitExecutorContent);

        shell_exec('chmod +x ' . $gitExecutorShellFile);
        shell_exec('bash ' . $gitExecutorShellFile . ' >> ' . $gitExecutorShellFileLog . ' &');

    }

    public function getLog()
    {
        $findDomain = Domain::find($this->domain_id);
        if (!$findDomain) {
            return 'Domain not found';
        }

        $shellLog = $findDomain->domain_root . '/git/tmp/git-action-' . $this->id . '.log';
        if (file_exists($shellLog)) {
            $content =  file_get_contents($shellLog);
            return nl2br($content);
        }

        return 'No logs';
    }

}
