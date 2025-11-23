<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side -->
            <div class="flex items-center gap-6">
                <!-- Logo / App name -->
                <a href="{{ route('timesheet') }}" class="flex items-center gap-2">
                    <span class="text-lg font-bold text-gray-900">
                        Awoo TimeTracker
                    </span>
                </a>

                <!-- Top links (optional, keep minimal because sidebar exists) -->
                <div class="hidden sm:flex sm:items-center sm:gap-2">
                    <a href="{{ route('timesheet') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition
                       {{ request()->routeIs('timesheet') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                        Timesheet
                    </a>

                    @if(auth()->user()?->is_admin)
                        <a href="{{ route('admin.projects') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition
                           {{ request()->routeIs('admin.projects') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                            Admin
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- User Dropdown -->
                <div class="relative ml-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition">
                                <div>{{ auth()->user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11l3.71-3.77a.75.75 0 111.08 1.04l-4.25 4.32a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Profile -->
                            @if(Route::has('profile.edit'))
                                <x-dropdown-link :href="route('profile.edit')">
                                    Profile
                                </x-dropdown-link>
                            @endif

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                              class="hidden"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t bg-white">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('timesheet') }}"
               class="block px-3 py-2 rounded-md text-base font-medium transition
               {{ request()->routeIs('timesheet') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                Timesheet
            </a>

            @if(auth()->user()?->is_admin)
                <a href="{{ route('admin.projects') }}"
                   class="block px-3 py-2 rounded-md text-base font-medium transition
                   {{ request()->routeIs('admin.projects') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                    Project Manager
                </a>

                <a href="{{ route('admin.reports') }}"
                   class="block px-3 py-2 rounded-md text-base font-medium transition
                   {{ request()->routeIs('admin.reports') ? 'text-blue-700 bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                    Monthly Reports
                </a>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t px-4">
            <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>

            <div class="mt-3 space-y-1">
                @if(Route::has('profile.edit'))
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profile
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
