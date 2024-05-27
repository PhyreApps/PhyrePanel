<div>

    <div class="mt-[2rem]">
        <h1>Tools</h1>
    </div>

    <div class="py-[2rem]">
        <div class="dark:bg-white/10 bg-black/5 rounded-xl">
            <div class="max-w-7xl mx-auto py-3 px-2 sm:px-4">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center">
                        <span class="flex p-2 rounded-lg dark:bg-white/20 bg-white">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                          </svg>
                        </span>
                        <p class="ml-3 font-medium dark:text-white text-black truncate">
                            <span class="hidden md:inline"> <span class="font-bold">DEVELOPMENT LICENSE:</span> This server is running on a development license. If this server is being used in a production environment, please notify <span class="text-blue-500 underline">phyre@cpanel.net</span></span>
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
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="grid grid-cols-3 dark:bg-[#1d2327] flex justify-between shadow-md rounded-xl">
            <div>
                <img class="lg:h-[16rem] w-full rounded-tl-xl rounded-bl-xl" src="{{asset('images/banner/wordpress-pattern.png')}}" alt="wordpress">
            </div>

            <div class="py-2 ml-[2rem] ">
                <h1 class="font-bold mt-2 mb-2">Create your website with</h1>
                <div class="py-2 dark:bg-white w-[14rem]">
                    <img class="h-[2rem] max-w-full rounded-xl" src="{{asset('images/banner/wordpress-logo.png')}}" alt="wordpress">
                </div>

                <ul class="list-disc mt-2">
                    <li>Lightning-fast hosting</li>
                    <li>Drag-and-drop functionality</li>
                    <li>Customize everything</li>
                </ul>
                <div class="mt-[1rem]">
                    <button class="dark:bg-white/10 bg-black/5  p-3 rounded-xl dark:hover:bg-blue-500 hover:bg-blue-500  transition duration-200"> Click to get started</button>
                </div>
            </div>
            <div>
                <img class="lg:h-[16rem] max-w-full rounded-tr-xl rounded-br-xl" src="{{asset('images/banner/wordpress.png')}}" alt="wordpress">
            </div>

        </div>
    </div>

    <div class="grid grid-cols-3">

        <div class="col-span-2">
        @foreach($menu as $menuItem)

            <div class="bg-white/10 mt-[2rem] rounded-xl px-2 shadow-md">
                <div class="flex justify-between">
                    <div class="flex gap-4 p-[1rem]">
                        <div class="mt-1">
                            @svg($menuItem['icon'], "h-12 w-12 text-red-600")
                        </div>
                        <div class="mt-[1rem]">{{$menuItem['title']}}</div>
                    </div>
                    <div class="p-[2rem]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                <div class="border-t dark:border-white/10 border-black/10"></div>

                <div class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1">
                    @foreach($menuItem['menu'] as $menuItemLink)

                        <div class="dark:text-white text-black hover:text-blue-500 px-[1rem] py-4 mt-2">
                            <a href="{{$menuItemLink['link']}}" class="flex">
                                <div class="">
                                    @svg($menuItemLink['icon'], "h-12 w-12 text-red-600")
                                </div>
                                <div class="ml-2 mt-2.5">{{$menuItemLink['title']}}</div>
                            </a>
                        </div>

                    @endforeach
                </div>
            </div>
        @endforeach
        </div>


        <div class="">
            <div class="p-[1rem] dark:bg-white/10 bg-white/50 ml-[2rem] mt-[2rem] rounded-xl">
                <h1 class="font-bold mt-[1.5rem]">General Information</h1>

                <p class="mt-[2rem] dark:text-white/80 text-black/50">Current User</p>
                <p class="">bochko</p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Primary Domain</p>
                <div class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <p class="text-blue-400"> 10iskata.microweber.me </p>
                </div>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Shared IP Address</p>
                <p class="">88.99.25.96</p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Home Directory</p>
                <p class="">/home/iskatami </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Last Login IP Address</p>
                <p class="">46.55.227.119</p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Primary Domain</p>
                <div class="flex gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="size-6 cursor-pointer hover:text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                    </svg>
                    <p class="text-blue-400">3d4e7dd6-251c-4c8d..</p>
                </div>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>


                <p class="pt-[1rem] dark:text-white/80 text-black/50">Theme</p>
                <div>
                    <div class="mt-1">
                        <input type="email" name="email" id="email" class="bg-white/10 shadow-sm focus:ring-none focus:border-none block w-full sm:text-sm border-none rounded-md" placeholder="your theme">
                    </div>
                </div>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>


                <div class="flex justify-between pt-[1rem]">
                    <p class="text-blue-400">Server Information</p>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </div>

                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>



            </div>


            <div class="p-[1rem] mt-[2rem] dark:bg-white/10 bg-white/50 ml-[2rem] mt-[2rem] rounded-xl">
                <h1 class="font-bold">Statistic</h1>

                <p class="mt-[2rem] dark:text-white/80 text-black/50">Disk Usage</p>
                <p class="">718.43 MB / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Database Disk Usage</p>
                <p class="">2.36 MB / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Bandwidth</p>
                <p class="">3.97 MB / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Addon Domains</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Subdomains</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Alias Domains</p>
                <p class="">1 / ∞  </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Email Accounts</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Mailing Lists</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Autoresponders</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Forwarders</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Email Filters</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">FTP Accounts</p>
                <p class="">0 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>

                <p class="pt-[1rem] dark:text-white/80 text-black/50">Databases</p>
                <p class="">1 / ∞ </p>
                <div class="border-b dark:border-white/10 border-black/40 pt-[1rem]"></div>
            </div>
        </div>
    </div>


    <div class="py-4">
        <p class="text-white/50">&copy; 2024 Phyre Hosting Panel. All rights reserved.</p>
    </div>

</div>
