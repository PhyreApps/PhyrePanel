<div>

    <div class="mt-[2rem]">
        <div class="dark:bg-white/10 bg-black/5 rounded-xl">
            <div class="max-w-7xl mx-auto py-3 px-2 sm:px-4">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center">
                        <span class="flex p-2 rounded-lg dark:bg-white/20 bg-white">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ml-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                          </svg>
                        </span>
                        <p class="px-2 font-medium dark:text-white text-black truncate">
                            <span class="hidden md:inline"> <span class="font-bold">DEVELOPMENT LICENSE:</span> If this server is being used in a production environment, notify <span class="text-blue-500 underline cursor-pointer">phyre@panel.net</span></span>
                        </p>
                    </div>
                    <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                        <a href="#" class="flex items-center justify-center px-4 py-2 rounded-md shadow-sm text-sm font-medium
                         dark:text-white dark:bg-white/20 bg-white dark:hover:bg-blue-500 hover:bg-blue-500
                         dark:hover:text-white transition duration-200">
                            Learn more
                        </a>
                    </div>
                    <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                        <button type="button" class="-mr-1 flex p-2 rounded-md hover:bg-blue-500 sm:-mr-2">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-6 w-6 text-white ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-[1.5rem] text-2xl">
        <h1>Tools</h1>
    </div>


    <div>
        <div class="grid sm:grid-cols-3">


            <div class="col-span-2">
                <div class=" sm:block hidden">
                    <div class="grid grid-cols-3 bg-gradient-to-l from-[#3a1dc8]/50 from-40% to-[#010afc]/40 flex justify-between shadow-md rounded-xl">
                        <div>
                            <img class="lg:h-[10rem] sm:h-[7.5rem]  w-full rounded-tl-xl rounded-bl-xl" src="{{asset('images/banner/wordpress.jpg')}}" alt="wordpress">
                        </div>

                        <div class="lg:py-4 px-4 dark:bg-none">
                            <h1 class="font-bold lg:py-2 py-1 text-center text-white xl:text-lg md:text-md sm:text-sm">Create your website with WordPress</h1>

                            <div class="px-[1rem] ml-4">
                                <button class="bg-white/10 p-2 xl:text-lg md:text-md sm:text-sm rounded-xl text-white hover:bg-white hover:text-black transition duration-500"> Click to get started</button>
                            </div>
                        </div>
                        <div class="">
                            <img class="lg:h-[10rem] sm:h-[7.5rem] w-[17rem] rounded-tr-xl rounded-br-xl" src="{{asset('images/banner/wordpress-themes.png')}}" alt="wordpress">
                        </div>

                    </div>
                </div>

                @foreach($menu as $menuItem)
                    <div x-data="{ open: false }" class="bg-white/10 mt-[2rem] rounded-xl px-2 shadow-sm  transition duration-500
                    hover:shadow-lg cursor-pointer">
                        <div  x-on:click="open = ! open" class="flex justify-between items-center">
                            <div class="flex gap-4 p-[1rem]">
                                <div class="mt-1">
                                    @svg($menuItem['icon'], "h-12 w-12 text-black dark:text-white")
                                </div>
                                <div class="mt-[1rem]">{{$menuItem['title']}}</div>
                            </div>
                            <div class="p-[1rem]">
                                <button>
                                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                    </svg>

                                </button>

                            </div>
                        </div>

                        <div class="border-t dark:border-white/10 border-black/10"></div>

                        <div x-show="open" x-transition.duration.500ms class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1">
                            @foreach($menuItem['menu'] as $menuItemLink)

                                <div class="dark:text-white text-black hover:scale-105 hover:bg-white/5 hover:rounded-xl
                                 transition duration-500 dark:hover:text-blue-400 hover:text-blue-500 px-[1rem] py-4 mt-2">
                                    <a href="{{$menuItemLink['link']}}" class="flex gap-[1rem] items-center">
                                        <div class="">
                                            @svg($menuItemLink['icon'], "h-12 w-12 text-blue dark:text-white")
                                        </div>
                                        <div class="items-center">{{$menuItemLink['title']}}</div>
                                    </a>
                                </div>

                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>


            <div class="sm:mt-0 mt-[2rem]">
                <div class="p-[1rem] dark:bg-white/10 bg-white/50 ml-[2rem] shadow-md rounded-xl">
                    <h1 class="font-bold mt-[1.5rem]">General Information</h1>

                    <p class="mt-[2rem] dark:text-white/80 text-black/50">Current User</p>
                    <p class="">bochko</p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Primary Domain</p>
                    <div class="flex gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <p class="text-blue-400"> 10iskata.microweber.me </p>
                    </div>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Shared IP Address</p>
                    <p class="">88.99.25.96</p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Home Directory</p>
                    <p class="">/home/iskatami </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Last Login IP Address</p>
                    <p class="">46.55.227.119</p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Primary Domain</p>
                    <div class="flex gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                             class="size-6 cursor-pointer hover:text-blue-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                        </svg>
                        <p class="text-blue-400">3d4e7dd6-251c-4c8d..</p>
                    </div>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>


                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Theme</p>
                    <div>
                        <div class="mt-1">
                            <input type="email" name="email" id="email" placeholder="your theme"
                            class="bg-white/10 shadow-sm focus:ring-none focus:border-none
                            block w-full sm:text-sm border-none rounded-md
                             dark:focus-ring dark:focus:ring-yellow-300">
                        </div>
                    </div>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>


                    <div class="flex justify-between pt-[1rem]">
                        <p class="text-blue-400 mb-2">Server Information</p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </div>


                <div class="p-[1rem] mt-[2rem] dark:bg-white/10 bg-white/50 ml-[2rem]  shadow-md rounded-xl">
                    <h1 class="font-bold mt-[1.5rem]">Statistic</h1>

                    <p class="mt-[2rem] dark:text-white/80 text-black/50">Disk Usage</p>
                    <p class="">718.43 MB / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Database Disk Usage</p>
                    <p class="">2.36 MB / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Bandwidth</p>
                    <p class="">3.97 MB / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Addon Domains</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Subdomains</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Alias Domains</p>
                    <p class="">1 / ∞  </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Email Accounts</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Mailing Lists</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Autoresponders</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Forwarders</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Email Filters</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">FTP Accounts</p>
                    <p class="">0 / ∞ </p>
                    <div class="border-b dark:border-white/10 border-black/5 pt-[1rem]"></div>

                    <p class="pt-[1rem] dark:text-white/80 text-black/50">Databases</p>
                    <p class="">1 / ∞ </p>
                </div>
            </div>
        </div>
    </div>



    <div class="py-4">
        <p class="text-white/50">&copy; 2024 Phyre Hosting Panel. All rights reserved.</p>
    </div>

</div>
