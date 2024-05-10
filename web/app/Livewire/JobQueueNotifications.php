<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class JobQueueNotifications extends Component
{

    public function render(): View
    {

        $jobs = [];
        $getJobs = DB::table('jobs')->orderBy('id', 'desc')->get();
        foreach ($getJobs as $job) {
            $job->payload = json_decode($job->payload);
            if (isset($job->payload->displayName)) {
                $displayName = 'Unknown Job';
                $displayDisplayDescription = '';

                try {

                    $jobClassInstance = new \ReflectionClass($job->payload->displayName);
                    if ($jobClassInstance->getStaticPropertyValue('displayName')) {
                        $displayName = $jobClassInstance->getStaticPropertyValue('displayName');
                    }
                    if ($jobClassInstance->getStaticPropertyValue('displayDescription')) {
                        $displayDisplayDescription = $jobClassInstance->getStaticPropertyValue('displayDescription');
                    }

                } catch (\Exception $e) {
                    $explodeDisplayName = explode('\\', $job->payload->displayName);
                    $displayName = end($explodeDisplayName);
                }

                $jobs[] = [
                    'id' => $job->id,
                    'displayName' => $displayName,
                    'displayDescription' => $displayDisplayDescription,
                    'status' => $job->attempts == 0 ? 'Pending' : 'Processing',
                    'createdAt' => $job->created_at
                ];

            }
        }

        return view('filament.job-queue-notifications', [
            'jobs' => $jobs
        ]);
    }

}
