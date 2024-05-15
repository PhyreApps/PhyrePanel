<x-filament-panels::page>

    <div class="grid grid-cols-3 gap-4 gap-y-8 bg-white/5 p-8 shadow rounded-xl">
        @foreach($linkGroups as $group)

            <div>
                <div class="flex gap-2 items-center">
                    <x-filament::icon-button size="xl" icon="{{$group['icon']}}" />
                    <div class="text-primary-500 text-xl">
                        {{ $group['title'] }}
                    </div>
                </div>
                <div class="pl-[2.1rem] mt-2">
                    <ul class="flex flex-col gap-y-1">
                    @foreach($group['links'] as $link)
                        <li>
                            <a class="cursor-pointer font-medium hover:text-primary-500 transition" href="{{$link['url']}}">
                                {{$link['title']}}
                            </a>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>

        @endforeach
    </div>

</x-filament-panels::page>
