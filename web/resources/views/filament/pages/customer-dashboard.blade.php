<div>


    <meta http-equiv="refresh" content="60">


    <div>
        <h1>Tools</h1>
    </div>

    <div>
        @foreach($menu as $menuItem)

            <div class="bg-white/10 mt-[2rem] rounded px-2 shadow-xl">
                <div class="flex justify-between py-4">
                    <div class="flex gap-2 px-4">
                        <div class=""><x-filament::icon-button size="xl" icon="{{$menuItem['icon']}}" /></div>
                        <div class="">{{$menuItem['title']}}</div>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-2"></div>
                <div class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1">
                    @foreach($menuItem['menu'] as $menuItemLink)

                        <div class="text-blue-400 hover:text-yellow-500 px-2">
                            <a href="{{$menuItemLink['link']}}" class="flex px-2 py-2">
                                <div class="text-white">
                                    <x-filament::icon-button size="xl" icon="{{$menuItemLink['icon']}}" />
                                </div>
                                <div class="ml-2">{{$menuItemLink['title']}}</div>
                            </a>
                        </div>

                    @endforeach
                </div>
            </div>
        @endforeach
            <div class="py-4">
                <p class="text-white/50  ">&copy; 2024 Phyre Hosting Panel. All rights reserved.</p>
            </div>
    </div>

</div>
