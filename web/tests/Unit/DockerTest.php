<?php

namespace tests\Unit;

use App\Models\User;
use Livewire\Livewire;
use Modules\Docker\Filament\Clusters\Docker\Pages\DockerCatalog;
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

        $livewireCatalogIndex = Livewire::test(DockerCatalog::class)
            ->set('keyword', 'nginx')
            ->assertSee('nginx');

        $viewData = $livewireCatalogIndex->viewData('dockerImages');
        $this->assertNotEmpty($viewData);

        $livewireCatalogIndex->set('keyword', 'non-existing-image')
            ->assertDontSee('non-existing-image');

    }

}
