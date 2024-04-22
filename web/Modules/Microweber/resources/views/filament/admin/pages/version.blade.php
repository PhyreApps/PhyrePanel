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

    <x-filament::button wire:click="checkForUpdates" class="w-[16rem]">
        Check for updates
    </x-filament::button>

</x-filament-panels::page>
