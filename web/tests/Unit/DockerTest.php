<?php

namespace tests\Unit;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;
use Modules\Docker\App\Models\DockerContainer;
use Modules\Docker\Filament\Clusters\Docker\Pages\DockerCatalog;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\CreateDockerContainer;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\EditDockerContainer;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\ListDockerContainers;
use Modules\Docker\PostInstall;
use Tests\TestCase;

class DockerTest extends TestCase
{
    public function testDockerImages()
    {
        $docker = new PostInstall();
        $docker->setLogFile('/tmp/phyrepanel-docker-install.log');
        $docker->run();

        $dockerIsInstalled = false;
        for ($i = 0; $i < 50; $i++) {
            $logFile = file_get_contents('/tmp/phyrepanel-docker-install.log');
            if (strpos($logFile, 'Done!') !== false) {
                $dockerIsInstalled = true;
                break;
            }
            sleep(1);
        }
        $this->assertTrue($dockerIsInstalled);

        $this->actingAs(User::factory()->create());

        $dockerImage = 'nginx';

        $dockerCatalogTest = Livewire::test(DockerCatalog::class);
        $livewireCatalogIndex = $dockerCatalogTest->set('keyword', $dockerImage)
            ->assertSee($dockerImage);

        $viewData = $livewireCatalogIndex->viewData('dockerImages');
        $this->assertNotEmpty($viewData);

        $livewireCatalogIndex->set('keyword', 'non-existing-image')
            ->assertDontSee('non-existing-image');

        $pullDockerImage = $dockerCatalogTest->call('pullDockerImage', $dockerImage)
            ->assertSee('Pull Docker Image');

        $isDokerImagePulled = false;
        $pullLog = '';
        for ($i = 0; $i < 300; $i++) {
            $pullLog = @file_get_contents($pullDockerImage->get('pullLogFile'));
            if (strpos($pullLog, 'DONE!') !== false) {
                $isDokerImagePulled = true;
                break;
            }
            sleep(1);
        }
        $this->assertTrue($isDokerImagePulled);

        $this->assertNotEmpty($pullLog);
        $this->assertStringContainsString('DONE!', $pullLog);

    }

    public function testDockerContainers()
    {
        $createDockerContainerTest = Livewire::test(CreateDockerContainer::class);
        $createDockerContainerTest->assertSee('Create Docker Container');

        $dockerName = 'nginx-latest-phyre-'.rand(1111,9999);
        $create = $createDockerContainerTest->fillForm([
            'name' => $dockerName,
            'image' => 'nginx',
            'environmentVariables' => [
                'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
                'NGINX_VERSION' => '1.25.5',
                'NJS_VERSION' => '0.8.4',
                'NJS_RELEASE' => '2~bookworm',
                'PKG_RELEASE' => '1~bookworm',
            ],
            'volumeMapping' => [],
            'port' => '83',
            'externalPort' => '3000',
        ])->call('create');

        $this->assertDatabaseHas(DockerContainer::class, [
            'name' => $dockerName,
        ]);

        $listDockerContainersTest = Livewire::test(ListDockerContainers::class);
        $listDockerContainersTest->assertSee($dockerName);

        $findDockerContainer = DockerContainer::where('name', $dockerName)->first();

        $editDockerContainersTest = Livewire::test(EditDockerContainer::class, [
            'record'=> $findDockerContainer->id
        ]);
        $editDockerContainersTest->assertSee('Edit Docker Container');
        $editDockerContainersTest->callAction(DeleteAction::class);

        $this->assertModelMissing($findDockerContainer);

    }
}
