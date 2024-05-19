<div>

    <div>
        <h1>Tools</h1>
    </div>

    <div>
        @foreach($menu as $menuItem)

            <div>
                <div>
                    {{$menuItem['title']}}
                </div>
            </div>

        @endforeach
    </div>

</div>
