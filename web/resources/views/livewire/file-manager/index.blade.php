<div>

    <div>
    <div class="my-2">

        <x-filament::button wire:click="back" icon="heroicon-o-arrow-uturn-left">
            Back
        </x-filament::button>

        <x-filament::button wire:click="refresh" icon="heroicon-o-arrow-path">
            Refresh
        </x-filament::button>


        <x-filament::modal id="create-folder">
            <x-slot name="trigger">
                <x-filament::button icon="heroicon-o-plus">
                    Create Folder
                </x-filament::button>
            </x-slot>
            <x-slot name="heading">
                Create Folder
            </x-slot>
            <x-slot name="footer">
                <div class="mb-2">
                    <x-filament::input.wrapper>
                        <x-filament::input placeholder="Folder name" type="text" wire:model="folderName"
                                           wire:keydown.enter="createFolder"/>
                    </x-filament::input.wrapper>
                </div>
                <x-filament::button wire:click="createFolder">
                    Create
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

        <x-filament::modal id="create-file">
            <x-slot name="trigger">
                <x-filament::button icon="heroicon-o-pencil">
                    Create File
                </x-filament::button>
            </x-slot>
            <x-slot name="heading">
                Create File
            </x-slot>
            <x-slot name="footer">
                <div class="mb-2">

                </div>
                <x-filament::button wire:click="createFile">
                    Create
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

        <x-filament::button wire:click="upload" icon="heroicon-o-arrow-up-tray">
            Upload
        </x-filament::button>

    </div>

    <div>
        <x-filament::modal id="delete-file">
            <x-slot name="heading">
                Are you sure you want to delete this file?
            </x-slot>
            <x-slot name="footer">
                <x-filament::button wire:click="delete">
                    Delete
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    <div class="overflow-hidden bg-white shadow ring-1 ring-black ring-opacity-5 md:rounded-lg mt-4">


        <table class="w-full">
            <thead>
            <tr>
                <th class="p-4">#</th>
                <th class="p-4 text-left">Name</th>
                <th class="p-4 text-left">Size</th>
                <th class="p-4 text-left">Last modified</th>
                <th class="p-4 text-left">Owner/Group</th>
                <th class="p-4 text-left">Permission</th>
                <th class="p-4 text-left"></th>
            </tr>
            </thead>
            <tbody>

            @if($canIBack)
                <tr wire:click="back" class="transition border border-slate-200 cursor-pointer hover:bg-gray-100 p-4">
                    <td colspan="6" class="p-4">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                      d="m4 10l-.707.707L2.586 10l.707-.707zm17 8a1 1 0 1 1-2 0zM8.293 15.707l-5-5l1.414-1.414l5 5zm-5-6.414l5-5l1.414 1.414l-5 5zM4 9h10v2H4zm17 7v2h-2v-2zm-7-7a7 7 0 0 1 7 7h-2a5 5 0 0 0-5-5z"/>
                            </svg>
                            Back to parent folder
                        </div>
                    </td>
                </tr>
            @endif

            @if(!empty($files))
                @foreach($files as $file)
                    <tr
                        class="transition border border-slate-200 cursor-pointer hover:bg-gray-100 p-4">
                        <td class="w-6 p-4" wire:click="goto('{{$file['name']}}')" wire:key="icon-{{md5($file['name'])}}">
                            <div>
                                @if($file['name'] == 'public_html')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-blue-500"
                                         viewBox="0 0 2048 2048">
                                        <path fill="currentColor"
                                              d="M496 883q13 0 29 4t32 11t32 13t29 12l-16 2q-8 1-17 1q-17 0-31-5t-26-13t-24-12t-22-6q-10 0-18 4t-16 9q0-4-7-4q7-7 26-11t29-5m135 45q41 0 75 14q-14 5-28 8t-29 4q-20 0-36-4q5-8 10-10t8-12M1024 0q141 0 271 37t244 103t208 161t160 207t104 244t37 272q0 141-37 271t-103 244t-161 208t-207 160t-244 104t-272 37q-141 0-271-37t-244-103t-208-161t-160-207t-104-244t-37-272q0-141 37-271t103-244t161-208t207-160T752 37t272-37m762 555q-14-22-28-42t-29-41q-2 9-5 13t-4 18q0 9 7 17t18 16t22 12t19 7m-69-98q0 8-3 11h6q4 0 6 1zm-693 1463q114 0 223-29t206-82t180-130t145-172q-13-30-25-61t-12-64q0-36 3-58t7-39t4-29t-3-31t-17-41t-37-62q1-7 3-19t4-25t1-24t-5-19q-26-3-54-11t-50-24l6-5q-13 3-26 8t-25 11t-26 8t-27 4l-16-2l3-7q-14 4-30 10t-31 6q-10 0-29-7t-38-17t-34-22t-15-23l2-3q-5-6-13-11t-15-10t-13-11t-5-14l11-9l-23-3l-8-30q2 5 9 4t11-4l-36-19l25-64q-14-52-7-80t27-46t44-36t49-49l-3-12l66-80l15-2q28 0 63-2t71-7t71-10t64-13q-32-38-67-72t-75-65q-11 4-27 11t-32 18t-25 24t-11 27l6 19q-18 29-40 36t-45 8t-48 0t-48 9l-16-34l15-58l-17-25l173-54q-11-28-36-42t-55-14v-10l56-9q-93-46-193-70t-205-24q-87 0-172 17t-164 49t-153 80t-135 108q26 0 40 13t26 29t25 29t35 14l16-12l-2-22l33-47l-26-74q5-3 15-10t17-7q30 0 46 3t28 11t21 23t28 38l36-28q10 4 32 13t45 22t39 27t17 26q0 15-11 24t-29 15t-37 9t-38 8t-29 10t-12 17l58 19q-20 17-43 31t-48 26l4 17l-92 36v28l-7 3l5-35l-4-1q-7 0-8 3t-1 7t2 8t1 6l-13-7l2 4q0 3 3 9t8 11t8 10t4 5q0 3-4 6t-10 4t-8 3t0 1q14 0 6 2t-25 10t-31 23t-16 44q0 17 1 33t-1 33q-14-38-42-58t-68-20l-43 4l21 14q-17-2-35-4t-37-1t-34 8t-30 21l-6 45q0 32 14 52t49 21q30 0 59-9t57-21q-9 22-20 42t-16 44l13 6q24-16 44-5t39 32t39 43t43 32l-34 18l-80-45q1 2 2 9t-1 3l-36-61q-32-1-68-10t-73-24t-69-33t-59-38l-7 107q0 122 33 238t93 218t147 186t193 143q-5-21-1-42t10-42t13-42t7-43q0-32-10-67t-24-71t-31-71t-27-66t-16-58t6-47l-15-7q6-14 16-27t21-26t17-28t7-30q0-10-4-21t-7-21l21 5q17-39 46-53t73-15q5 0 21 4t34 11t34 11t24 8q0 7 8 9t9 7l-2 8q3 1 14 7t24 15t23 16t14 11q18 0 49 12t68 30t73 43t68 50t49 50t20 44l-34 36q4 51-7 78t-34 45t-53 30t-65 34q0 20-10 43t-25 44t-36 35t-42 14l-42-32q2 2 0 7t-5 2q10 19 5 44t-17 51t-27 49t-27 39q54 14 108 21t109 7"/>
                                    </svg>
                                @elseif($file['is_dir'])
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-primary-500"
                                         viewBox="0 0 256 256">
                                        <path fill="currentColor"
                                              d="M216 72h-84.69L104 44.69A15.88 15.88 0 0 0 92.69 40H40a16 16 0 0 0-16 16v144.62A15.41 15.41 0 0 0 39.39 216h177.5A15.13 15.13 0 0 0 232 200.89V88a16 16 0 0 0-16-16M40 56h52.69l16 16H40Z"/>
                                    </svg>
                                @elseif($file['extension'] == 'zip')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 24 24">
                                        <path fill="none" stroke="currentColor" stroke-width="2"
                                              d="M4.998 9V1H19.5L23 4.5V23H4M18 1v5h5M2 13h5v1l-4 4v1h5m3-7v8zm4 1v7zm5 2a2 2 0 0 0-2-2h-3v4h3a2 2 0 0 0 2-2Z"/>
                                    </svg>
                                @elseif ($file['extension'] == 'txt')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 16 16">
                                        <path fill="currentColor" fill-rule="evenodd"
                                              d="M14 4.5V14a2 2 0 0 1-2 2h-2v-1h2a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM1.928 15.849v-3.337h1.136v-.662H0v.662h1.134v3.337zm4.689-3.999h-.894L4.9 13.289h-.035l-.832-1.439h-.932l1.228 1.983l-1.24 2.016h.862l.853-1.415h.035l.85 1.415h.907l-1.253-1.992zm1.93.662v3.337h-.794v-3.337H6.619v-.662h3.064v.662H8.546Z"/>
                                    </svg>
                                @elseif($file['extension'] == 'gz')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 2048 2048">
                                        <path fill="currentColor"
                                              d="M1792 0q27 0 50 10t40 27t28 41t10 50v480q0 45-9 77t-24 58t-31 46t-31 40t-23 44t-10 55v992q0 27-10 50t-27 40t-41 28t-50 10H256V0zM640 128v384h256V128zm1024 800q0-31-9-54t-24-44t-31-41t-31-45t-23-58t-10-78V128h-512v512H768v128H640V640H512V128H384v1792h384v-128h128v128h768zm128-800h-128v480q0 24 4 42t13 33t20 29t27 32q15-17 26-31t20-30t13-33t5-42zM640 896h128v128H640zm0 256h128v128H640zm0 256h128v128H640zm128 256v128H640v-128zm0-768V768h128v128zm0 256v-128h128v128zm0 256v-128h128v128zm0 256v-128h128v128z"/>
                                    </svg>
                                @elseif($file['extension'] == 'php')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 16 16">
                                        <path fill="currentColor" fill-rule="evenodd"
                                              d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM1.6 11.85H0v3.999h.791v-1.342h.803q.43 0 .732-.173q.305-.175.463-.474a1.4 1.4 0 0 0 .161-.677q0-.375-.158-.677a1.2 1.2 0 0 0-.46-.477q-.3-.18-.732-.179m.545 1.333a.8.8 0 0 1-.085.38a.57.57 0 0 1-.238.241a.8.8 0 0 1-.375.082H.788V12.48h.66q.327 0 .512.181q.185.182.185.522m4.48 2.666V11.85h-.79v1.626H4.153V11.85h-.79v3.999h.79v-1.714h1.682v1.714zm.703-3.999h1.6q.433 0 .732.179q.3.175.46.477q.158.302.158.677t-.161.677q-.159.299-.463.474a1.45 1.45 0 0 1-.733.173H8.12v1.342h-.791zm2.06 1.714a.8.8 0 0 0 .084-.381q0-.34-.184-.521q-.184-.182-.513-.182h-.66v1.406h.66a.8.8 0 0 0 .375-.082a.57.57 0 0 0 .237-.24Z"/>
                                    </svg>
                                @elseif($file['extension'] == 'html')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 16 16">
                                        <path fill="currentColor" fill-rule="evenodd"
                                              d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zm-9.736 7.35v3.999h-.791v-1.714H1.79v1.714H1V11.85h.791v1.626h1.682V11.85h.79Zm2.251.662v3.337h-.794v-3.337H4.588v-.662h3.064v.662zm2.176 3.337v-2.66h.038l.952 2.159h.516l.946-2.16h.038v2.661h.715V11.85h-.8l-1.14 2.596H9.93L8.79 11.85h-.805v3.999zm4.71-.674h1.696v.674H12.61V11.85h.79v3.325Z"/>
                                    </svg>
                                @elseif($file['extension'] == 'css')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 16 16">
                                        <path fill="currentColor" fill-rule="evenodd"
                                              d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM3.397 14.841a1.13 1.13 0 0 0 .401.823q.195.162.478.252q.284.091.665.091q.507 0 .859-.158q.354-.158.539-.44q.187-.284.187-.656q0-.336-.134-.56a1 1 0 0 0-.375-.357a2 2 0 0 0-.566-.21l-.621-.144a1 1 0 0 1-.404-.176a.37.37 0 0 1-.144-.299q0-.234.185-.384q.188-.152.512-.152q.214 0 .37.068a.6.6 0 0 1 .246.181a.56.56 0 0 1 .12.258h.75a1.1 1.1 0 0 0-.2-.566a1.2 1.2 0 0 0-.5-.41a1.8 1.8 0 0 0-.78-.152q-.439 0-.776.15q-.337.149-.527.421q-.19.273-.19.639q0 .302.122.524q.124.223.352.367q.228.143.539.213l.618.144q.31.073.463.193a.39.39 0 0 1 .152.326a.5.5 0 0 1-.085.29a.56.56 0 0 1-.255.193q-.167.07-.413.07q-.175 0-.32-.04a.8.8 0 0 1-.248-.115a.58.58 0 0 1-.255-.384zM.806 13.693q0-.373.102-.633a.87.87 0 0 1 .302-.399a.8.8 0 0 1 .475-.137q.225 0 .398.097a.7.7 0 0 1 .272.26a.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964a1.4 1.4 0 0 0-.489-.272a1.8 1.8 0 0 0-.606-.097q-.534 0-.911.223q-.375.222-.572.632q-.195.41-.196.979v.498q0 .568.193.976q.197.407.572.626q.375.217.914.217q.439 0 .785-.164t.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.8.8 0 0 1-.118.363a.7.7 0 0 1-.272.25a.9.9 0 0 1-.401.087a.85.85 0 0 1-.478-.132a.83.83 0 0 1-.299-.392a1.7 1.7 0 0 1-.102-.627zM6.78 15.29a1.2 1.2 0 0 1-.111-.449h.764a.58.58 0 0 0 .255.384q.106.073.25.114q.142.041.319.041q.245 0 .413-.07a.56.56 0 0 0 .255-.193a.5.5 0 0 0 .085-.29a.39.39 0 0 0-.153-.326q-.152-.12-.463-.193l-.618-.143a1.7 1.7 0 0 1-.539-.214a1 1 0 0 1-.351-.367a1.1 1.1 0 0 1-.123-.524q0-.366.19-.639q.19-.272.527-.422t.777-.149q.456 0 .779.152q.326.153.5.41q.18.255.2.566h-.75a.56.56 0 0 0-.12-.258a.6.6 0 0 0-.246-.181a.9.9 0 0 0-.37-.068q-.324 0-.512.152a.47.47 0 0 0-.184.384q0 .18.143.3a1 1 0 0 0 .404.175l.621.143q.326.075.566.211t.375.358t.135.56q0 .37-.188.656a1.2 1.2 0 0 1-.539.439q-.351.158-.858.158q-.381 0-.665-.09a1.4 1.4 0 0 1-.478-.252a1.1 1.1 0 0 1-.29-.375"/>
                                    </svg>
                                @elseif($file['extension'] == 'js')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 16 16">
                                        <path fill="currentColor" fill-rule="evenodd"
                                              d="M14 4.5V14a2 2 0 0 1-2 2H8v-1h4a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM3.186 15.29a1.2 1.2 0 0 1-.111-.449h.765a.58.58 0 0 0 .255.384q.105.073.249.114q.143.041.319.041q.246 0 .413-.07a.56.56 0 0 0 .255-.193a.5.5 0 0 0 .085-.29a.39.39 0 0 0-.153-.326q-.151-.12-.462-.193l-.619-.143a1.7 1.7 0 0 1-.539-.214a1 1 0 0 1-.351-.367a1.1 1.1 0 0 1-.123-.524q0-.366.19-.639q.19-.272.528-.422q.336-.15.776-.149q.457 0 .78.152q.324.153.5.41q.18.255.2.566h-.75a.56.56 0 0 0-.12-.258a.6.6 0 0 0-.247-.181a.9.9 0 0 0-.369-.068q-.325 0-.513.152a.47.47 0 0 0-.184.384q0 .18.143.3a1 1 0 0 0 .405.175l.62.143q.327.075.566.211q.24.136.375.358t.135.56q0 .37-.188.656a1.2 1.2 0 0 1-.539.439q-.351.158-.858.158q-.381 0-.665-.09a1.4 1.4 0 0 1-.478-.252a1.1 1.1 0 0 1-.29-.375m-3.104-.033A1.3 1.3 0 0 1 0 14.791h.765a.6.6 0 0 0 .073.27a.5.5 0 0 0 .454.246q.285 0 .422-.164q.138-.165.138-.466v-2.745h.79v2.725q0 .66-.357 1.005q-.354.345-.984.345a1.6 1.6 0 0 1-.569-.094a1.15 1.15 0 0 1-.407-.266a1.1 1.1 0 0 1-.243-.39"/>
                                    </svg>
                                @elseif($file['extension'] == 'json')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 16 16">
                                        <path fill="currentColor" fill-rule="evenodd"
                                              d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM4.151 15.29a1.2 1.2 0 0 1-.111-.449h.764a.58.58 0 0 0 .255.384q.105.073.25.114q.142.041.319.041q.245 0 .413-.07a.56.56 0 0 0 .255-.193a.5.5 0 0 0 .084-.29a.39.39 0 0 0-.152-.326q-.152-.12-.463-.193l-.618-.143a1.7 1.7 0 0 1-.539-.214a1 1 0 0 1-.352-.367a1.1 1.1 0 0 1-.123-.524q0-.366.19-.639q.192-.272.528-.422q.337-.15.777-.149q.456 0 .779.152q.326.153.5.41q.18.255.2.566h-.75a.56.56 0 0 0-.12-.258a.6.6 0 0 0-.246-.181a.9.9 0 0 0-.37-.068q-.324 0-.512.152a.47.47 0 0 0-.185.384q0 .18.144.3a1 1 0 0 0 .404.175l.621.143q.326.075.566.211a1 1 0 0 1 .375.358q.135.222.135.56q0 .37-.188.656a1.2 1.2 0 0 1-.539.439q-.351.158-.858.158q-.381 0-.665-.09a1.4 1.4 0 0 1-.478-.252a1.1 1.1 0 0 1-.29-.375m-3.104-.033a1.3 1.3 0 0 1-.082-.466h.764a.6.6 0 0 0 .074.27a.5.5 0 0 0 .454.246q.285 0 .422-.164q.137-.165.137-.466v-2.745h.791v2.725q0 .66-.357 1.005q-.355.345-.985.345a1.6 1.6 0 0 1-.568-.094a1.15 1.15 0 0 1-.407-.266a1.1 1.1 0 0 1-.243-.39m9.091-1.585v.522q0 .384-.117.641a.86.86 0 0 1-.322.387a.9.9 0 0 1-.47.126a.9.9 0 0 1-.47-.126a.87.87 0 0 1-.32-.387a1.55 1.55 0 0 1-.117-.641v-.522q0-.386.117-.641a.87.87 0 0 1 .32-.387a.87.87 0 0 1 .47-.129q.265 0 .47.129a.86.86 0 0 1 .322.387q.117.255.117.641m.803.519v-.513q0-.565-.205-.973a1.46 1.46 0 0 0-.59-.63q-.38-.22-.916-.22q-.534 0-.92.22a1.44 1.44 0 0 0-.589.628q-.205.407-.205.975v.513q0 .562.205.973q.205.407.589.626q.386.217.92.217q.536 0 .917-.217q.384-.22.589-.626q.204-.41.205-.973m1.29-.935v2.675h-.746v-3.999h.662l1.752 2.66h.032v-2.66h.75v4h-.656l-1.761-2.676z"/>
                                    </svg>
                                @elseif($file['extension'] == 'jpg' || $file['extension'] == 'jpeg' || $file['extension'] == 'png' || $file['extension'] == 'gif')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 256 256">
                                        <path fill="currentColor"
                                              d="M109 148.67a6 6 0 0 0-10 0L76.46 182.5l-11.41-17.74a6 6 0 0 0-10.1 0l-36 56A6 6 0 0 0 24 230h128a6 6 0 0 0 5-9.33ZM35 218l25-38.9l11.32 17.6a6 6 0 0 0 10 .08l22.64-34L140.79 218ZM212.24 83.76l-56-56A6 6 0 0 0 152 26H56a14 14 0 0 0-14 14v88a6 6 0 0 0 12 0V40a2 2 0 0 1 2-2h90v50a6 6 0 0 0 6 6h50v122a2 2 0 0 1-2 2h-8a6 6 0 0 0 0 12h8a14 14 0 0 0 14-14V88a6 6 0 0 0-1.76-4.24M158 46.48L193.52 82H158Z"/>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 text-purple-400"
                                         viewBox="0 0 256 256">
                                        <path fill="currentColor"
                                              d="m213.66 82.34l-56-56A8 8 0 0 0 152 24H56a16 16 0 0 0-16 16v176a16 16 0 0 0 16 16h144a16 16 0 0 0 16-16V88a8 8 0 0 0-2.34-5.66M152 88V44l44 44Z"/>
                                    </svg>
                                @endif
                            </div>
                        </td>
                        <td class="p-4" wire:click="goto('{{$file['name']}}')" wire:key="name-{{md5($file['name'])}}">
                            {{ $file['name'] }}
                        </td>
                        <td class="p-4">
                            {{ $file['size'] }}
                        </td>
                        <td class="p-4">
                            {{$file['last_modified']}}
                        </td>
                        <td class="p-4">
                            {{$file['owner']}} / {{$file['group']}}
                        </td>
                        <td class="p-4">
                            {{$file['permission']}}
                        </td>
                        <td>
                            <x-filament::dropdown placement="bottom-start">
                                <x-slot name="trigger">
                                    <button type="button" onclick="e.preventDefault()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a1 1 0 1 0 2 0a1 1 0 1 0-2 0m7 0a1 1 0 1 0 2 0a1 1 0 1 0-2 0m7 0a1 1 0 1 0 2 0a1 1 0 1 0-2 0" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-filament::dropdown.list>
                                    <x-filament::dropdown.list.item wire:click="openViewModal" icon="heroicon-m-eye" icon-color="primary">
                                        View
                                    </x-filament::dropdown.list.item>

                                    <x-filament::dropdown.list.item wire:click="openEditModal" icon="heroicon-m-pencil" icon-color="primary">
                                        Edit
                                    </x-filament::dropdown.list.item>

                                    <x-filament::dropdown.list.item wire:click="openDeleteModal" icon="heroicon-m-trash" icon-color="danger">
                                        Delete
                                    </x-filament::dropdown.list.item>
                                </x-filament::dropdown.list>
                            </x-filament::dropdown>
                        </td>
                    </tr>
                @endforeach
            @endif

            </tbody>
        </table>
    </div>

</div>
</div>
