<div>

    <script>
        let hiddenJobQueueNotifications = [];
        function hideJobQueueNotification(jobId) {
            hiddenJobQueueNotifications.push(jobId);
            document.getElementById('job-queue-' + jobId).style.display = 'none';
            document.cookie = "hideJobQueueIds=" + JSON.stringify(hiddenJobQueueNotifications);
        }
    </script>

    <div class="flex flex-col gap-y-2 bottom-4 right-4 w-[24rem] fixed z-50">
@foreach($jobs as $job)

    <div id="job-queue-{{$job['id']}}" class="rounded-xl text-black dark:text-white px-4 py-4 shadow-sm bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

        <div class="w-full flex justify-between">
            <div class="text-gray-500 dark:text-gray-400">
                {{ $job['displayName'] }}
            </div>
           <div>
                <svg onclick="hideJobQueueNotification('{{$job['id']}}')" xmlns="http://www.w3.org/2000/svg" class="w-4 text-gray-500 dark:text-gray-400 cursor-pointer" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m7 7l10 10M7 17L17 7" />
                </svg>
            </div>
        </div>

        <div class="flex gap-2 items-center mt-2">
            <div class="text-sm">
                @if(!empty($job['displayDescription']))
                    {{ $job['displayDescription'] }}
                @else
                    Running...
                @endif
            </div>
        </div>

        <div class="animate-pulse mt-2">
            <div class="h-1 bg-primary-500 rounded-xl"></div>
        </div>

    </div>

@endforeach
    </div>
</div>
