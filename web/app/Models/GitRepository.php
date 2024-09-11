<?php

namespace App\Models;

use App\GitClient;
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
    ];


    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function clone()
    {
        $this->status = self::STATUS_CLONING;
        $this->save();

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

        $gitSSHKey = GitSshKey::find($this->git_ssh_key_id);
        if ($gitSSHKey) {
            $sshPath = '/home/'.$findHostingSubscription->system_username .'/.git-ssh/'.$gitSSHKey->id;
            $privateKeyFile = $sshPath.'/id_rsa';
            $publicKeyFile = $sshPath.'/id_rsa.pub';

            if (!is_dir($sshPath)) {
                shell_exec('mkdir -p ' . $sshPath);
                shell_exec('chown '.$findHostingSubscription->system_username.':'.$findHostingSubscription->system_username.' -R ' . dirname($sshPath));
                shell_exec('chmod 0700 ' . dirname($sshPath));
            }

            if (!file_exists($privateKeyFile)) {
                file_put_contents($privateKeyFile, $gitSSHKey->private_key);
                chown($privateKeyFile, $findHostingSubscription->system_username);
                chmod($privateKeyFile, 0400);
            }

            if (!file_exists($publicKeyFile)) {
                file_put_contents($publicKeyFile, $gitSSHKey->public_key);
                chown($publicKeyFile, $findHostingSubscription->system_username);
                chmod($publicKeyFile, 0400);
            }
        }

        $gitSSHUrl = GitClient::parseGitUrl($this->url);
        if (!isset($gitSSHUrl['provider'])) {
            $this->status = self::STATUS_FAILED;
            $this->status_message = 'Provider not found';
            $this->save();
            return;
        }

        $cloneUrl = 'git@'.$gitSSHUrl['provider'].':'.$gitSSHUrl['owner'].'/'.$gitSSHUrl['name'].'.git';

        $shellCommand = [];

        if ($gitSSHKey) {
            $shellCommand[] = 'git -c core.sshCommand="ssh -i '.$privateKeyFile .'" clone '.$cloneUrl.' '.$projectDir;
        } else {
            $shellCommand[] = 'git clone '.$cloneUrl.' '.$projectDir;
        }

        $shellCommand[] = 'phyre-php /usr/local/phyre/web/artisan git-repository:mark-as-cloned '.$this->id;

        $shellContent = '';
        foreach ($shellCommand as $command) {
            $shellContent .= $command . "\n";
        }

        $shellFile = '/tmp/git-clone-' . $this->id . '.sh';
        $shellLog = '/tmp/git-clone-' . $this->id . '.log';
        
        file_put_contents($shellFile, $shellContent);

        shell_exec('chmod +x ' . $shellFile);
        shell_exec('bash '.$shellFile.' >> ' . $shellLog . ' &');

    }

}
