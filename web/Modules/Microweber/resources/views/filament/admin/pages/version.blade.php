<x-filament-panels::page>

    <x-filament::card>
        <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">
                Microweber App Version
        </h3>
        <p>
            Your app version is: {{ $appVersion }}<br>
            Latest app version is: {{ $latestAppVersion }}<br>
            Latest app download date: {{ $latestAppDownloadDate }}
        </p>
    </x-filament::card>

    <x-filament::card>
        <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">
           Microweber App Templates
          </h3>

        <p>
            Available App Templates ({{ $totalAppTemplates }})
        </p>

        <div class="mt-4">
            @foreach ($appTemplates as $appTemplate)
                <span>{{ $appTemplate['name'] }}</span>
                @if (!$loop->last)
                    ,
                @endif
            @endforeach
        </div>
    </x-filament::card>


   {{-- <div class="p-4 text-white rounded bg-green-500/90 dark:bg-green-500/30">
        Your app and templates is up-to-date!
    </div>--}}

    @if($downloadingNow)
        <div class="flex gap-2 items-center p-4 text-white rounded bg-green-500/90 dark:bg-green-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-dasharray="15" stroke-dashoffset="15" stroke-linecap="round" stroke-width="2" d="M12 3C16.9706 3 21 7.02944 21 12">
                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.3s" values="15;0" />
                    <animateTransform attributeName="transform" dur="1.5s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12" />
                </path>
            </svg>
            Downloading new app version...
        </div>
    @else
    <x-filament::button wire:click="checkForUpdates" class="w-[16rem]">
        Check for updates
    </x-filament::button>
    @endif

</x-filament-panels::page>
