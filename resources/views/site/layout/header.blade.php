<header class=" border-b border-auc-text-color-500">
    <div class="auc-container py-[12px] min-1200:py-[32px]">
        <div class="grid-cols-12 items-center grid font-jost">
            <div class="col-span-8 min-576:col-span-4 min-992:col-span-4">
                <a href="/">
                    <h3 class="text-auc-primary-color">{{ env('APP_NAME') }}</h3>
                </a>
            </div>
            <div class="min-992:col-span-5 h-full hidden min-992:block">
                <ul class="flex items-center justify-center h-full">
                    <li
                        class="text-[18px] h-full flex items-center font-medium hover:text-auc-primary-color mr-[20px] min-1200:mr-[40px] last:mr-0">
                        <a id="change-address">
                            @if (isset($address))
                                {{ $address['area'] }}
                            @else
                                Set your location
                            @endif
                        </a>
                    </li>
                    <li
                        class="text-[18px] h-full flex items-center font-medium hover:text-auc-primary-color mr-[20px] min-1200:mr-[40px] last:mr-0">
                        <a href="{{ route('checkBooking') }}">Availability</a>
                    </li>
                    <li
                        class="text-[18px] h-full flex items-center font-medium hover:text-auc-primary-color mr-[20px] min-1200:mr-[40px] last:mr-0">
                        <a href="{{ route('cart.index') }}">Cart({{ $cart_product }})</a>
                    </li>
                    <li
                        class="text-[18px] h-full flex items-center font-medium hover:text-auc-primary-color mr-[20px] min-1200:mr-[40px] last:mr-0">
                        <a href="{{ route('siteFAQs.index') }}">FAQs</a>
                    </li>
                    <li
                        class="text-[18px] h-full flex items-center font-medium hover:text-auc-primary-color mr-[20px] min-1200:mr-[40px] last:mr-0">
                        <a href="{{ route('siteReviews.index') }}">Reviews</a>
                    </li>
                    <li
                        class="text-[18px] h-full flex items-center font-medium hover:text-auc-primary-color mr-[20px] min-1200:mr-[40px] last:mr-0">
                        <a href="{{ route('contactUs') }}">Contact</a>
                    </li>
                </ul>
            </div>
            <div class="col-span-4 min-576:col-span-8 min-992:col-span-3 flex justify-end gap-4 items-center">
                @guest
                    <div class="justify-end hidden min-992:flex">
                        <a class="auc-btn-default auc-btn-primary" href="{{ route('customer.login') }}">Login</a>
                    </div>
                @else
                    <div id="menu-toggle" class="relative py-[8px] hidden min-992:block">
                        <div
                            class="flex items-center justify-between px-[16px] py-[12px] rounded-[8px] w-40 ml-auto bg-auc-primary-color-300">
                            <span
                                class="rounded-[50%] border border-auc-primary-color w-[38px] h-[38px] bg-cover bg-center bg-no-repeat bg-lightgray"
                                style="background-image: url(&quot;/profile.png&quot;);"></span>
                            <span class="text-[16px]">{{ auth()->user()->name }}</span>
                            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </div>

                        <nav id="menu"
                            class="flex flex-col min-w-[240px] font-sans text-base font-normal [&>div]:rounded-none z-30 p-0 overflow-hidden w-[250px] gap-0 absolute opacity-0 invisible ease-in-out top-[88px] right-0 bg-auc-white-color rounded-[8px] false"
                            style="box-shadow: rgba(0, 0, 0, 0.12) 0px 0px 40px 20px;">
                            <div role="button"
                                class="items-center w-full rounded-lg text-start leading-tight hover:text-auc-primary-color hover:bg-opacity-100 outline-none block p-0">
                                <a class="flex items-center gap-[16px] px-[24px] py-[18px]" href="/dashboard">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" class=" text-slate-400" fill="currentColor"><path d="M22.0604 0H14.9509C13.8797 0 13.0112 0.868428 13.0112 1.93969V9.04922C13.0112 10.1205 13.8797 10.9889 14.9509 10.9889H22.0604C23.1317 10.9889 24.0001 10.1205 24.0001 9.04922V1.93969C24.0001 0.868428 23.1317 0 22.0604 0Z"></path><path d="M9.04922 0H1.93969C0.868428 0 0 0.868428 0 1.93969V9.04922C0 10.1205 0.868428 10.9889 1.93969 10.9889H9.04922C10.1205 10.9889 10.9889 10.1205 10.9889 9.04922V1.93969C10.9889 0.868428 10.1205 0 9.04922 0Z"></path><path d="M22.0604 13.011H14.9509C13.8797 13.011 13.0112 13.8794 13.0112 14.9507V22.0602C13.0112 23.1315 13.8797 23.9999 14.9509 23.9999H22.0604C23.1317 23.9999 24.0001 23.1315 24.0001 22.0602V14.9507C24.0001 13.8794 23.1317 13.011 22.0604 13.011Z"></path><path d="M9.04922 13.011H1.93969C0.868428 13.011 0 13.8794 0 14.9507V22.0602C0 23.1315 0.868428 23.9999 1.93969 23.9999H9.04922C10.1205 23.9999 10.9889 23.1315 10.9889 22.0602V14.9507C10.9889 13.8794 10.1205 13.011 9.04922 13.011Z"></path></svg>
                                    <span class="text-[18px] leading-[120%] font-regular">Dashboard</span>
                                </a>
                            </div>
                            <div role="button"
                                class="items-center w-full rounded-lg text-start leading-tight hover:text-auc-primary-color hover:bg-opacity-100 outline-none block p-0">
                                <a class="flex items-center gap-[16px] px-[24px] py-[18px]"
                                    href="/dashboard/profile-settings">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-slate-400">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z" clip-rule="evenodd" />
                                      </svg>
                                    <span class="text-[18px] leading-[120%] font-regular">Profile Setting</span>
                                </a>
                            </div>
                            <div role="button"
                                class="items-center w-full rounded-lg text-start leading-tight hover:text-auc-primary-color hover:bg-opacity-100 outline-none block p-0">
                                <a class="flex items-center gap-[16px] px-[24px] py-[18px]"
                                    href="/dashboard/change-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-slate-400">
                                        <path fill-rule="evenodd" d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.251l-1.267 1.113a7.047 7.047 0 0 1 0 2.228l1.267 1.113a1 1 0 0 1 .206 1.25l-1.18 2.045a1 1 0 0 1-1.187.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.251l1.267-1.114a7.05 7.05 0 0 1 0-2.227L1.821 7.773a1 1 0 0 1-.206-1.25l1.18-2.045a1 1 0 0 1 1.187-.447l1.598.54A6.992 6.992 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                      </svg>
                                    <span class="text-[18px] leading-[120%] font-regular">Change Password</span>
                                </a>
                            </div>
                            <div role="button"
                                class="items-center w-full rounded-lg text-start leading-tight hover:text-auc-primary-color hover:bg-opacity-100 outline-none block p-0">
                                <button class="flex items-center gap-[16px] px-[24px] py-[18px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-slate-400">
                                        <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd" />
                                      </svg>
                                    <span class="text-[18px] leading-[120%] font-regular"><a
                                            href="{{ route('customer.logout') }}">Logout</a>
                                    </span>
                                </button>
                            </div>
                        </nav>
                    </div>
                @endguest
                <!-- Mobile Menu Toggle -->
                <div class="min-992:hidden">
                    <button id="mobile-menu-toggle"
                        class="p-[10px] ms-4 gap-8px flex-shrink-[0] align-middle bg-auc-primary-color hover:bg-auc-primary-color-900 rounded-[8px] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" class="w-[30px] h-[30px]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="mobile-menu" tabindex="-1"
                class="fixed z-[9999] bg-white box-border w-full shadow-2xl shadow-blue-gray-900/10 top-0 left-0 p-[20px] transform -translate-x-full transition-transform">
                <div class="mb-4 flex items-center justify-between">
                    <a href="/">
                        <h3 class="text-auc-primary-color">{{ env('APP_NAME') }}</h3>
                    </a>
                    <button id="mobile-menu-close"
                        class="p-[10px] bg-auc-primary-color hover:bg-auc-primary-color-900 rounded-[8px] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" class="w-[24px] h-[24px]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="gap-1 flex flex-col">
                    <a href="/about"
                        class="block py-2 px-3 rounded-lg text-[18px] font-medium hover:bg-blue-gray-50 hover:text-auc-primary-color">About</a>
                    <a href="/product"
                        class="block py-2 px-3 rounded-lg text-[18px] font-medium hover:bg-blue-gray-50 hover:text-auc-primary-color">Auction</a>
                    <a href="/faq"
                        class="block py-2 px-3 rounded-lg text-[18px] font-medium hover:bg-blue-gray-50 hover:text-auc-primary-color">FAQs</a>
                    <a href="/contact"
                        class="block py-2 px-3 rounded-lg text-[18px] font-medium hover:bg-blue-gray-50 hover:text-auc-primary-color">Contact</a>
                    <a href="/plans"
                        class="block py-2 px-3 rounded-lg text-[18px] font-medium hover:bg-blue-gray-50 hover:text-auc-primary-color">Plans</a>
                </nav>
                <div class="mt-4">
                    <a href="/login"
                        class="block w-full py-3 text-center text-white bg-auc-primary-color hover:bg-auc-primary-color-900 rounded-lg">Login</a>
                </div>
            </div>
        </div>
    </div>
</header>