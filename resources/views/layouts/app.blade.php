<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            {{-- Top navigation (your existing Livewire nav) --}}
            <livewire:layout.navigation />

            {{-- Optional page heading (your existing header block) --}}
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- Main area with sidebar + content --}}
            <div class="flex">

                {{-- Sidebar --}}
                <aside class="w-60 shrink-0 bg-white border-r min-h-[calc(100vh-4rem)] p-4 space-y-5">
                    <div class="space-y-2">
                        <div class="text-gray-500 font-semibold uppercase text-xs tracking-wider">
                            My Work
                        </div>

                        <a href="{{ route('timesheet') }}"
                           class="block px-3 py-2 rounded-md transition
                           {{ request()->routeIs('timesheet') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                            Timesheet
                        </a>
                    </div>

                    @if(auth()->user()?->is_admin)
                        <div class="pt-3 border-t"></div>

                        <div class="space-y-2">
                            <div class="text-gray-500 font-semibold uppercase text-xs tracking-wider">
                                Admin
                            </div>

                            <a href="{{ route('admin.projects') }}"
                               class="block px-3 py-2 rounded-md transition
                               {{ request()->routeIs('admin.projects') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                Project Manager
                            </a>

                            <a href="{{ route('admin.reports') }}"
                               class="block px-3 py-2 rounded-md transition
                               {{ request()->routeIs('admin.reports') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                Monthly Reports
                            </a>
                        </div>
                    @endif
                </aside>

                {{-- Page Content (your existing slot) --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>

            </div>
        </div>

        @livewireScripts
    </body>
</html>