<x-filament-panels::page>

    <x-filament::modal width="4xl" id="install-module-modal">

        <x-filament::modal.heading>
            Install Module
        </x-filament::modal.heading>

        <x-filament::modal.description>
            <div class="">
                @if ($this->installLogPulling)
                    <div class="w-full">
                        <div id="js-install-log" wire:poll="getInstallLog"
                             class="text-left text-sm font-medium text-gray-950 dark:text-yellow-500 h-[20rem] overflow-y-scroll">
                            {!! $this->installLog !!}
                        </div>

                        <script>
                            window.setInterval(function() {
                                var elem = document.getElementById('js-install-log');
                                elem.scrollTop = elem.scrollHeight;
                            }, 2000);
                        </script>
                    </div>
                @endif
            </div>
        </x-filament::modal.description>

    </x-filament::modal>

    <div class="">

        @foreach($categories as $categoryName=>$modules)

            <h3 class="mb-4">
                {{$categoryName}}
            </h3>

            <div class="grid grid-cols-3 gap-6 mb-6">
        @foreach($modules as $module)
            <div class="sm:flex gap-2 px-6 py-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="mb-2">
                    <div class="w-12">
                        <x-filament::icon :icon="$module['logoIcon']"
                                          class="w-12 h-12 text-primary-500"/>
                    </div>
                </div>
                <div class="flex justify-between w-full">
                    <div class="flex flex-col">
                        <p>
                            {{$module['name']}}
                        </p>

                        <p class="text-xs mt-1">
                            {{$module['category']}}
                        </p>
                    </div>
                    <div>
                        <x-filament::dropdown>
                            <x-slot name="trigger">
                                <button>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 20 20">
                                        <path fill="currentColor" d="M10 6a1.25 1.25 0 1 1 0-2.5A1.25 1.25 0 0 1 10 6m0 5.25a1.25 1.25 0 1 1 0-2.5a1.25 1.25 0 0 1 0 2.5m-1.25 4a1.25 1.25 0 1 0 2.5 0a1.25 1.25 0 0 0-2.5 0"></path>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-filament::dropdown.list>
                                <x-filament::dropdown.list.item wire:click="openInstallModal('{{$module['name']}}')">
                                    <div class="flex gap-2 items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24">
                                            <path fill="currentColor" d="M11 2v5H8l4 4l4-4h-3V2h7a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm8 14H5v4h14zm-2 1v2h-2v-2z"></path>
                                        </svg>
                                        Install
                                    </div>
                                </x-filament::dropdown.list.item>
                            </x-filament::dropdown.list>
                        </x-filament::dropdown>
                    </div>
                </div>
        </div>
        @endforeach

            </div>
        @endforeach

    </div>

</x-filament-panels::page>
