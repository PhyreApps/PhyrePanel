<div>
@foreach($jobs as $job)

    <div class="fixed z-50 bottom-5 right-5 w-[22rem] rounded-xl text-black dark:text-white px-4 py-4 shadow-sm bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

        <div class="w-full flex justify-between">
            <div class="text-sm">
                Runnning...
            </div>
           {{-- <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m7 7l10 10M7 17L17 7" />
                </svg>
            </div>--}}
        </div>

        <div class="flex gap-2 items-center">
            <div>
                {{ $job['displayName']}}
            </div>
        </div>

        <div class="animate-pulse mt-2">
            <div class="h-1 bg-primary-500 rounded-xl"></div>
        </div>

    </div>

@endforeach
</div>
