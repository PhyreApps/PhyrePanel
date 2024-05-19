<div>

    <div>
        <h1>Tools</h1>
    </div>

    <div>
        @foreach($menu as $menuItem)

            <div>
                <div>
                    <x-filament::icon-button size="xl" icon="{{$menuItem['icon']}}" />
                    {{$menuItem['title']}}
                </div>

                <div>
                    @foreach($menuItem['menu'] as $menuItemLink)

                        <div>
                            <a href="{{$menuItemLink['link']}}">
                                <x-filament::icon-button size="xl" icon="{{$menuItemLink['icon']}}" />
                                {{$menuItemLink['title']}}
                            </a>
                        </div>


                    @endforeach
                </div>
            </div>

        @endforeach
    </div>

</div>
