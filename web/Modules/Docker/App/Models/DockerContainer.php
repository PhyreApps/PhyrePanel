<?php

namespace Modules\Docker\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Docker\DockerApi;
use Modules\Docker\DockerContainerApi;

class DockerContainer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'command',
        'docker_id',
        'image',
        'labels',
        'local_volumes',
        'mounts',
        'names',
        'networks',
        'ports',
        'running_for',
        'size',
        'state',
        'status',
        'memory_limit',
        'unlimited_memory',
        'automatic_start',
        'external_port',
        'volume_mapping',
        'environment_variables',
    ];

    protected $casts = [
        'environment_variables' => 'array',
        'volume_mapping' => 'array',
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $dockerContainerApi = new DockerContainerApi();
            $dockerContainerApi->setImage($model->image);
            $dockerContainerApi->setEnvironmentVariables($model->environment_variables);
            $dockerContainerApi->setVolumeMapping($model->volume_mapping);
//            $dockerContainerApi->setMemoryLimit($model->memory_limit);
//            $dockerContainerApi->setUnlimitedMemory($model->unlimited_memory);
//            $dockerContainerApi->setAutomaticStart($model->automatic_start);
            $dockerContainerApi->setPort($model->port);
            $dockerContainerApi->setExternalPort($model->external_port);
            $createContainer = $dockerContainerApi->run();
            if (!isset($createContainer['ID'])) {
                return false;
            }

            $model->image = $createContainer['Image'];
            $model->command = $createContainer['Command'];
            $model->labels = $createContainer['Labels'];
            $model->local_volumes = $createContainer['LocalVolumes'];
            $model->mounts = $createContainer['Mounts'];
            $model->names = $createContainer['Names'];
            $model->networks = $createContainer['Networks'];
            $model->ports = $createContainer['Ports'];
            $model->running_for = $createContainer['RunningFor'];
            $model->size = $createContainer['Size'];
            $model->state = $createContainer['State'];
            $model->status = $createContainer['Status'];
            $model->docker_id = $createContainer['ID'];

        });

        static::deleting(function ($model) {
            $dockerApi = new DockerApi();
            $dockerApi->removeContainerById($model->docker_id);
        });
    }
}
