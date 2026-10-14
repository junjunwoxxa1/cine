<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="bg-gray-950 text-gray-100 font-sans antialiased min-h-screen">
    <!-- Header Navigation -->
    <header
        class="border-b border-gray-600/80 bg-gray-900/50 backdrop-blur-md sticky top-0 z-50 py-3.5 px-6 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
            <a href="{{ route('home') }}">
                <x-app-logo-icon class="max-w-20 fill-current text-white dark:text-black" />
            </a>
        </div>

        <!-- Search Bar & Actions -->
        <div class="flex items-center space-x-6" x-data="{ search: '' }">
            <div class="relative flex items-center">
                <svg class="w-4 h-4 absolute left-3.5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" x-model="search" placeholder="Buscar película..."
                    class="bg-gray-900 border border-gray-800 rounded-full pl-10 pr-4 py-1.5 text-sm w-64 text-gray-200 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all placeholder-gray-500">
            </div>
            @auth
                <div
                    class="border border-gray-700 bg-gray-800 hover:border-gray-500 rounded-full w-9 h-9 flex items-center justify-center font-bold text-xs uppercase cursor-pointer transition-all shadow-md">
                    G
                </div>
            @else
                <a href="{{ route('register') }}"
                    class="font-semibold text-sm text-gray-300 hover:text-white transition-colors flex items-center gap-1 hover:underline underline-offset-4">
                    Regsitrate
                </a>
                <a href="{{ route('login') }}"
                    class="font-semibold text-sm text-gray-300 hover:text-white transition-colors flex items-center gap-1 hover:underline underline-offset-4">
                    ¿Ya tenienes cuenta?
                </a>
            @endauth
        </div>
    </header>
    {{ $slot }}
    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
