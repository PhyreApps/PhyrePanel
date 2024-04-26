<x-filament-panels::page>

    <div class="">

        @foreach($categories as $categoryName=>$modules)

            <h3 class="mb-4">
                {{$categoryName}}
            </h3>

            <div class="grid grid-cols-3 gap-6 mb-6">
        @foreach($modules as $module)
        <x-filament::section>
            <a href="{{$module['url']}}">
            <div class="flex gap-3">

                <div class="mb-2">
                    <div class="w-12">
                        <x-filament::icon :icon="$module['logoIcon']"
                                          class="w-12 h-12 text-primary-500"/>
                    </div>
                </div>

                <div class="flex flex-col ">

                    <p>
                        {{$module['name']}}
                    </p>

                    <p class="text-xs mt-1">
                        {{$module['category']}}
                    </p>

                </div>
            </div>
            </a>
        </x-filament::section>
        @endforeach

            </div>
        @endforeach

    </div>

</x-filament-panels::page>
