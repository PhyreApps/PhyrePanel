@foreach($jobs as $job)

    <div class="absolute z-50 bottom-5 right-5 w-[22rem] rounded-xl text-black px-3 py-4 shadow-sm bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

        <div class="w-full flex justify-between">
            <div>
                {{ $job['displayName']}}
            </div>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m7 7l10 10M7 17L17 7" />
                </svg>
            </div>
        </div>

        <div class="flex gap-2 items-center">
            <span class="text-sm">Running...</span>
        </div>

    </div>

@endforeach
