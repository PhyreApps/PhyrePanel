<?php

namespace tests\Unit;

use App\Models\User;
use Livewire\Livewire;
use Modules\Docker\Filament\Clusters\Docker\Pages\DockerCatalog;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource;
use Modules\Docker\PostInstall;
use Tests\TestCase;

class DockerTest extends TestCase
{
    public function testDocker()
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

}
