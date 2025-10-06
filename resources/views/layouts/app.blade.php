<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Movie Context' }} - {{ config('app.name', 'Movie Context') }}</title>

    <!-- SEO Meta Tags -->
    @if(isset($seoDescription))
        <meta name="description" content="{{ $seoDescription }}">
    @endif
    @if(isset($seoKeywords))
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $title ?? 'Movie Context' }}">
    <meta property="og:description" content="{{ $seoDescription ?? 'Najnovšie filmové novinky, recenzie a správy z filmového sveta' }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Scripts -->
    @livewireStyles
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    <!-- Navigation -->
    <nav class="border-b border-gray-200 bg-white sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="text-xl font-semibold text-gray-900 hover:text-gray-700">
                        Movie Context
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:block">
                    <div class="flex items-center space-x-8">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'text-gray-900 border-b-2 border-gray-900' : '' }}">
                            Domov
                        </a>
                        <a href="#" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                            Recenzie
                        </a>
                        <a href="#" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                            Novinky
                        </a>
                        <a href="#" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                            Autori
                        </a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-gray-900 p-2" aria-expanded="false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden hidden border-t border-gray-200 bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 {{ request()->routeIs('home') ? 'text-gray-900 bg-gray-50' : '' }}">
                    Domov
                </a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900">
                    Recenzie
                </a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900">
                    Novinky
                </a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900">
                    Autori
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Movie Context</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        Najnovšie filmové novinky, recenzie a správy z filmového sveta.
                        Denné aktualizácie o najnovších filmoch, traileroch a filmových udalostiach.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Rýchle odkazy</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Najnovšie články</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Najčítanejšie</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Tagy</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Autori</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Newsletter</h4>
                    <p class="text-gray-600 text-sm mb-4">
                        Odoberajte týždenný newsletter s najväčšími filmovými novinkami.
                    </p>
                    <form class="space-y-2">
                        <input
                            type="email"
                            placeholder="Váš email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        >
                        <button
                            type="submit"
                            class="w-full bg-gray-900 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors"
                        >
                            Odoberať
                        </button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-600 text-sm">
                        © {{ date('Y') }} Movie Context. Všetky práva vyhradené.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">O nás</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Kontakt</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Podmienky</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @livewireScripts
    @stack('scripts')
</body>
</html>
