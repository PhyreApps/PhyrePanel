<div class="flex flex-col gap-y-6">

    <x-filament::card>

        <div class="flex items-center justify-between">
                <div class="ml-4 text-lg font-semibold leading-7">
                    <div class="text-white">
                        {{ $serverStats['memory']['total'] }}
                    </div>
                    <div class="text-sm text-gray-200">
                        Total Memory
                    </div>
                </div>
                <div class="ml-4 text-lg font-semibold leading-7">
                    <div class="text-white">
                        {{ $serverStats['memory']['used'] }}
                    </div>
                    <div class="text-sm text-gray-200">
                        Used Memory
                    </div>
                </div>
                <div class="ml-4 text-lg font-semibold leading-7">
                    <div class="text-white">
                        {{ $serverStats['memory']['free'] }}
                    </div>
                    <div class="text-sm text-gray-200">
                        Free Memory
                    </div>
                </div>
        </div>

    </x-filament::card>

    <x-filament::card>

        <div class="flex items-center gap-4">
            <div class="ml-4 text-lg font-semibold leading-7">
                <div class="text-white">
                    {{ $serverStats['memory']['shared'] }}
                </div>
                <div class="text-sm text-gray-200">
                    Shared Memory
                </div>
            </div>
            <div class="ml-4 text-lg font-semibold leading-7">
                <div class="text-white">
                    {{ $serverStats['memory']['buffCache'] }}
                </div>
                <div class="text-sm text-gray-200">
                    Buff Cache Memory
                </div>
            </div>
        </div>

    </x-filament::card>

</div>
