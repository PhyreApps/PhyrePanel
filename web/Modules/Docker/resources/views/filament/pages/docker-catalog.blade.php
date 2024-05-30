<div xmlns:x-filament="http://www.w3.org/1999/html">

    <div class=" mt-6">
        <x-filament::breadcrumbs :breadcrumbs="[
            '/' => 'Home',
            '/admin/docker' => 'Docker',
        ]" />
    </div>

    @if (!empty($dockerContainers))
    <div class="mt-6">
        <div class="text-xl">Your Containers</div>
        <div class="grid sm:grid-cols-2 gap-6 my-6">
            @foreach($dockerContainers as $dockerContainer)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="flex items-center gap-2 px-6 py-2 rounded-t-xl bg-black/5">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8" viewBox="0 0 32 32">
                                <path fill="#0096e6" d="M16.54 12.663h2.86v2.924h1.446a6.272 6.272 0 0 0 1.988-.333a5.091 5.091 0 0 0 .966-.436a3.584 3.584 0 0 1-.67-1.849a3.907 3.907 0 0 1 .7-2.753l.3-.348l.358.288a4.558 4.558 0 0 1 1.795 2.892a4.375 4.375 0 0 1 3.319.309l.393.226l-.207.4a4.141 4.141 0 0 1-4.157 1.983c-2.48 6.168-7.871 9.088-14.409 9.088c-3.378 0-6.476-1.263-8.241-4.259l-.029-.049l-.252-.519a8.316 8.316 0 0 1-.659-4.208l.04-.433h2.445v-2.923h2.861V9.8h5.721V6.942h3.432z"></path>
                                <path fill="currentColor" d="M12.006 24.567a6.022 6.022 0 0 1-3.14-3.089a10.329 10.329 0 0 1-2.264.343q-.5.028-1.045.028q-.632 0-1.331-.037a9.051 9.051 0 0 0 7 2.769q.392 0 .78-.014M7.08 13.346h.2v2.067h-.2Zm-.376 0h.2v2.067H6.7v-2.067Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.368 0h.2v2.067h-.2zM5 13.14h2.482v2.479H5Zm2.859-2.861h2.48v2.479H7.863Zm2.077.207h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.368 0h.2v2.066h-.2Zm-.207 2.653h2.48v2.48H7.863V13.14Zm2.077.207h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.368 0h.2v2.067h-.2Zm2.654-.207H13.2v2.48h-2.48V13.14Zm2.076.207H13v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.368 0h.2v2.067h-.2Zm-.206-3.067H13.2v2.479h-2.48v-2.479Zm2.076.207H13v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.368 0h.2v2.066h-.2Zm2.654 2.653h2.479v2.48h-2.48V13.14Zm2.076.207h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.368 0h.192v2.067h-.2v-2.067Zm-.206-3.067h2.479v2.479h-2.48zm2.076.207h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.368 0h.192v2.066h-.2v-2.066Zm-.206-3.067h2.479V9.9h-2.48zm2.076.206h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.376 0h.2v2.066h-.2Zm-.368 0h.192v2.066h-.2V7.625Zm2.654 5.514h2.479v2.48h-2.48V13.14Zm2.076.207h.195v2.067h-.2v-2.067Zm-.376 0h.206v2.067h-.206Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.2Zm-.376 0h.2v2.067h-.205v-2.067Zm-.368 0h.2v2.067h-.194v-2.067Zm-6.442 6.292a.684.684 0 1 1-.684.684a.684.684 0 0 1 .684-.684m0 .194a.489.489 0 0 1 .177.033a.2.2 0 1 0 .275.269a.49.49 0 1 1-.453-.3Z"></path>
                            </svg>
                        </div>
                        <div class="font-extralight text-xl">
                            {{ $dockerContainer['name'] }} #{{ $dockerContainer['id']}}
                        </div>
                        <div>
                            @if($dockerContainer['state'] == 'running')
                                <x-filament::badge color="success">
                                Running
                                </x-filament::badge>
                            @else
                                <x-filament::badge color="danger">
                                Stopped
                                </x-filament::badge>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <div>
                            Image: {{ $dockerContainer['image'] }}
                        </div>
                        @if (!empty($dockerContainer['port']))
                        <div>
                            Ports: {{ $dockerContainer['port'] }} -> {{ $dockerContainer['external_port'] }}
                        </div>
                        @endif
                        @if (!empty($dockerContainer['status']))
                            <div>
                                Status: {{ $dockerContainer['status'] }}
                            </div>
                        @endif
                        <div class="flex gap-3 mt-4">
                            <x-filament::button id="stop-docker-container-{{ $dockerContainer['id'] }}" wire:click="stopDockerContainer('{{ $dockerContainer['id'] }}')" size="xs">
                                Stop
                            </x-filament::button>
                            <x-filament::button id="restart-docker-container-{{ $dockerContainer['id'] }}" wire:click="restartDockerContainer('{{ $dockerContainer['id'] }}')" size="xs">
                                Restart
                            </x-filament::button>
                            <x-filament::button id="remove-docker-container-{{ $dockerContainer['id'] }}" wire:click="removeDockerContainer('{{ $dockerContainer['id'] }}')" size="xs" color="danger">
                                Remove
                            </x-filament::button>
                        </div>
                        <div class="mt-4">
                            <hr />
                        </div>
                        <div class="flex justify-between mt-4">
                            <x-filament::button id="docker-container-details-{{ $dockerContainer['id'] }}"

                                                tag="a"
                                                href="{{url('admin/docker/containers/'. $dockerContainer['id'])}}"

                                                size="md" outlined>
                                <div class="flex gap-2 items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m1 15h-2v-6h2zm0-8h-2V7h2z"></path>
                                    </svg>
                                    Details
                                </div>
                            </x-filament::button>
                            <x-filament::button id="docker-container-logs-{{ $dockerContainer['id'] }}"
                                                tag="a"
                                                href="{{url('admin/docker/containers/'. $dockerContainer['id'])}}"
                                                size="md" outlined>
                                <div class="flex gap-2 items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6" viewBox="0 0 15 15">
                                        <path fill="currentColor" d="M4.5 6.995H4v1h.5zm6 1h.5v-1h-.5zm-6 1.998H4v1h.5zm6 1.007h.5v-1h-.5zm-6-7.003H4v1h.5zM8.5 5H9V4h-.5zm2-4.5l.354-.354L10.707 0H10.5zm3 3h.5v-.207l-.146-.147zm-9 4.495h6v-1h-6zm0 2.998l6 .007v-1l-6-.007zm0-5.996L8.5 5V4l-4-.003zm8 9.003h-10v1h10zM2 13.5v-12H1v12zM2.5 1h8V0h-8zM13 3.5v10h1v-10zM10.146.854l3 3l.708-.708l-3-3zM2.5 14a.5.5 0 0 1-.5-.5H1A1.5 1.5 0 0 0 2.5 15zm10 1a1.5 1.5 0 0 0 1.5-1.5h-1a.5.5 0 0 1-.5.5zM2 1.5a.5.5 0 0 1 .5-.5V0A1.5 1.5 0 0 0 1 1.5z"></path>
                                    </svg>
                                    Logs
                                </div>
                            </x-filament::button>
                            <x-filament::button id="docker-container-settings-{{ $dockerContainer['id'] }}"

                                                tag="a"
                                                href="{{url('admin/docker/containers/'. $dockerContainer['id'])}}"

                                                size="md" outlined>
                                <div class="flex gap-2 items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6" viewBox="0 0 16 16">
                                        <path fill="currentColor" d="M2.267 6.153A6 6 0 0 1 3.53 3.98a.36.36 0 0 1 .382-.095l1.36.484a.71.71 0 0 0 .935-.538l.26-1.416a.35.35 0 0 1 .274-.282a6.1 6.1 0 0 1 2.52 0c.14.03.248.141.274.282l.26 1.416a.708.708 0 0 0 .935.538l1.36-.484a.36.36 0 0 1 .382.095a6 6 0 0 1 1.262 2.173a.35.35 0 0 1-.108.378l-1.102.931a.703.703 0 0 0 0 1.076l1.102.931c.11.093.152.242.108.378a6 6 0 0 1-1.262 2.173a.36.36 0 0 1-.382.095l-1.36-.484a.71.71 0 0 0-.935.538l-.26 1.416a.35.35 0 0 1-.275.282a6.1 6.1 0 0 1-2.519 0a.35.35 0 0 1-.275-.282l-.259-1.416a.708.708 0 0 0-.935-.538l-1.36.484a.36.36 0 0 1-.382-.095a6 6 0 0 1-1.262-2.173a.35.35 0 0 1 .108-.378l1.102-.931a.704.704 0 0 0 0-1.076l-1.102-.931a.35.35 0 0 1-.108-.378M6.25 8a1.75 1.75 0 1 0 3.5 0a1.75 1.75 0 0 0-3.5 0"></path>
                                    </svg>
                                    Settings
                                </div>
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="mt-6">
        <div class="text-xl mb-2">Docker Image Catalog</div>
        {{ $this->form }}
    </div>

    <x-filament::modal width="4xl" id="pull-docker-image">

        <x-filament::modal.heading>
            Pull Docker Image
        </x-filament::modal.heading>

        <x-filament::modal.description>
            <div class="">
                @if ($this->pullLogPulling)
                    <div class="w-full">
                        <div id="js-pull-log" wire:poll="getPullLog"
                             class="text-left text-sm font-medium text-gray-950 dark:text-yellow-500 h-[20rem] overflow-y-scroll">
                            {!! $this->pullLog !!}
                        </div>

                        <script>
                            window.setInterval(function() {
                                var elem = document.getElementById('js-pull-log');
                                elem.scrollTop = elem.scrollHeight;
                            }, 2000);
                        </script>
                    </div>
                @endif
            </div>
        </x-filament::modal.description>

    </x-filament::modal>

    <div class="grid sm:grid-cols-2 gap-6 my-6">
        @foreach($dockerImages as $dockerImage)
        <div class="sm:flex gap-2 p-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex flex-col items-center justify-center">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[6rem]" viewBox="0 0 32 32">
                        <path fill="#3a4e55" d="M18.191 13.071H20.7v2.566h1.27a5.5 5.5 0 0 0 1.744-.292a4.462 4.462 0 0 0 .848-.383a3.149 3.149 0 0 1-.589-1.623a3.427 3.427 0 0 1 .616-2.416l.264-.305l.314.253a4 4 0 0 1 1.575 2.538a3.837 3.837 0 0 1 2.913.271l.345.2l-.181.354a3.629 3.629 0 0 1-3.648 1.74c-2.173 5.413-6.9 7.976-12.642 7.976A7.958 7.958 0 0 1 6.3 20.211l-.025-.043l-.226-.459a7.28 7.28 0 0 1-.579-3.693l.035-.38h2.143v-2.565h2.51v-2.51h5.02v-2.51h3.012v5.02Z"></path>
                        <path fill="#00aada" d="M26.324 14.021a3.311 3.311 0 0 0-1.418-2.821a3.072 3.072 0 0 0 .289 3.821a5.279 5.279 0 0 1-3.225 1.037H5.883a6.779 6.779 0 0 0 .667 3.737l.183.335a6.2 6.2 0 0 0 .379.569q.992.064 1.829.045a8.972 8.972 0 0 0 2.669-.389a.193.193 0 1 1 .126.365c-.09.031-.184.061-.281.088a8.4 8.4 0 0 1-1.845.3c.044 0-.046.007-.046.007l-.082.007a21.455 21.455 0 0 1-2.008-.006l-.01.007a7.882 7.882 0 0 0 6.063 2.41c5.56 0 10.276-2.465 12.365-8c1.482.152 2.906-.226 3.553-1.49a3.5 3.5 0 0 0-3.122-.022"></path>
                        <path fill="#27b9ec" d="M26.324 14.021a3.311 3.311 0 0 0-1.418-2.821a3.072 3.072 0 0 0 .289 3.821a5.279 5.279 0 0 1-3.225 1.037H6.836a5.223 5.223 0 0 0 2.106 4.686a8.972 8.972 0 0 0 2.669-.389a.193.193 0 1 1 .126.365c-.09.031-.184.061-.281.088a8.83 8.83 0 0 1-1.894.314l-.019-.022c1.892.971 4.636.967 7.782-.241a21.868 21.868 0 0 0 9.1-6.889l-.1.048"></path>
                        <path fill="#088cb9" d="M5.913 17.732a6.431 6.431 0 0 0 .637 2.061l.183.335a6.2 6.2 0 0 0 .379.569q.992.064 1.829.045a8.972 8.972 0 0 0 2.669-.389a.193.193 0 1 1 .126.365c-.09.031-.184.061-.281.088a8.826 8.826 0 0 1-1.891.307h-.1c-.291.016-.6.026-.922.026c-.351 0-.709-.007-1.1-.026a7.913 7.913 0 0 0 6.076 2.413c4.76 0 8.9-1.807 11.3-5.8Z"></path>
                        <path fill="#039cc7" d="M6.98 17.732a4.832 4.832 0 0 0 1.961 3.01a8.972 8.972 0 0 0 2.669-.389a.193.193 0 1 1 .126.365c-.09.031-.184.061-.281.088a8.959 8.959 0 0 1-1.9.307c1.892.971 4.628.957 7.773-.252a20.545 20.545 0 0 0 5.377-3.13Z"></path>
                        <path fill="#00acd3" d="M9.889 13.671h.172v1.813h-.172zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813H9.23v-1.813Zm-.33 0h.179v1.813H8.9v-1.813Zm-.33 0h.179v1.813H8.57zm-.323 0h.172v1.813h-.17v-1.813Zm-.181-.181h2.175v2.176H8.066V13.49Zm4.335-2.329h.172v1.813H12.4zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.178v1.813h-.178zm-.323 0h.172v1.813h-.172zm-.181-.181h2.176v2.176h-2.175z"></path>
                        <path fill="#26c2ee" d="M12.4 13.671h.172v1.813H12.4zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.178v1.813h-.178zm-.323 0h.172v1.813h-.172zm-.181-.181h2.176v2.176h-2.175z"></path>
                        <path fill="#00acd3" d="M14.909 13.671h.172v1.813h-.172zm-.33 0h.179v1.813h-.178zm-.33 0h.179v1.813h-.178zm-.33 0h.181v1.813h-.179v-1.813Zm-.33 0h.179v1.813h-.179zm-.323 0h.172v1.813h-.172zm-.181-.181h2.176v2.176h-2.174V13.49Z"></path>
                        <path fill="#26c2ee" d="M14.909 11.161h.172v1.813h-.172zm-.33 0h.179v1.813h-.178zm-.33 0h.179v1.813h-.178zm-.33 0h.181v1.813h-.179v-1.813Zm-.33 0h.179v1.813h-.179zm-.323 0h.172v1.813h-.172zm-.181-.181h2.176v2.176h-2.174v-2.177Zm4.335 2.691h.172v1.813h-.172zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813H16.1zm-.323 0h.172v1.813h-.172zm-.177-.181h2.176v2.176H15.6z"></path>
                        <path fill="#00acd3" d="M17.42 11.161h.172v1.813h-.172zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813H16.1zm-.323 0h.172v1.813h-.172zm-.181-.181h2.176v2.176H15.6v-2.177Z"></path>
                        <path fill="#26c2ee" d="M17.42 8.65h.172v1.813h-.172zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813H16.1zm-.323 0h.172v1.813h-.172zm-.177-.181h2.176v2.176H15.6z"></path>
                        <path fill="#00acd3" d="M19.93 13.671h.17v1.813h-.17zm-.33 0h.178v1.813H19.6zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.33 0h.179v1.813h-.179zm-.323 0h.172v1.813h-.172zm-.181-.181h2.176v2.176h-2.175z"></path>
                        <path fill="#d5eef2" d="M12.616 19.193a.6.6 0 1 1-.6.6a.6.6 0 0 1 .6-.6"></path>
                        <path fill="#3a4e55" d="M12.616 19.363a.431.431 0 0 1 .156.029a.175.175 0 1 0 .241.236a.43.43 0 1 1-.4-.265M2 17.949h27.92c-.608-.154-1.923-.362-1.707-1.159c-1.105 1.279-3.771.9-4.444.267c-.749 1.087-5.111.674-5.415-.173c-.939 1.1-3.85 1.1-4.789 0c-.3.847-4.666 1.26-5.415.173c-.673.631-3.338 1.012-4.444-.267c.217.8-1.1 1.005-1.707 1.159"></path>
                        <path fill="#c0dbe1" d="M14.211 23.518a5.287 5.287 0 0 1-2.756-2.711a9.2 9.2 0 0 1-1.987.3q-.436.024-.917.025q-.554 0-1.168-.033a7.942 7.942 0 0 0 6.145 2.43q.344 0 .683-.013"></path>
                        <path fill="#d5eef2" d="M12.007 21.773a5.206 5.206 0 0 1-.552-.966a9.2 9.2 0 0 1-1.987.3a6.325 6.325 0 0 0 2.539.664"></path>
                    </svg>
                </div>
            </div>
            <div class="w-full flex flex-col justify-center">
                <div class="text-lg font-semibold">
                    {{ $dockerImage['name'] }}
                </div>
                <div class="text-sm mt-2">
                    {{ $dockerImage['description'] }}
                </div>
                <div class="flex justify-between items-center mt-4">
                    <div class="flex gap-2">
                    @if($dockerImage['is_official'])
                        <x-filament::badge color="success">
                            Official
                        </x-filament::badge>
                    @else
                        <x-filament::badge color="info">
                            Community
                        </x-filament::badge>
                    @endif
                        <x-filament::badge color="warning">
                            {{ $dockerImage['star_count'] }} Stars
                        </x-filament::badge>
                    </div>
                    <div>
                        <x-filament::button wire:key="docker-image-{{$dockerImage['id']}}" wire:click="pullDockerImage('{{ $dockerImage['name'] }}')" class="bg-blue-500/90 text-white">
                            Run
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
