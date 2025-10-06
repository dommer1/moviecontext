<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - Movie Context</title>
    <meta name="description" content="{{ $title }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }
        .single-post-content a { color: #00C6D3; text-decoration: underline; font-weight: 700; }
        .single-post-content h1 { font-size: 2.25rem; line-height: 2.5rem; font-weight: 700; margin-bottom: 2rem; }
        .single-post-content h2 { font-size: 1.5rem; line-height: 2rem; font-weight: 700; margin: 2rem 0 1rem 0; }
        .single-post-content p { margin-bottom: 1rem; line-height: 1.7; }
        .single-post-content ul { margin-bottom: 1rem; padding-left: 1.5rem; }
        .single-post-content li { margin-bottom: 0.5rem; line-height: 1.6; }
    </style>
</head>
<body class="bg-white text-gray-900">
    <!-- Navigation -->
    <header class="fixed top-0 inset-x-0 z-30">
        <div class="relative bg-white z-20">
            <div class="max-w-7xl w-full px-6 mx-auto">
                <div class="flex justify-between items-center gap-x-6 h-14 sm:h-18">
                    <a href="{{ route('home') }}" class="shrink-0">
                        <div class="font-bold text-xl">Movie Context</div>
                    </a>

                    <x-navigation />

                    <div class="flex items-center gap-x-6">
                        <div class="hidden xl:flex items-center gap-x-4">
                            <button type="button" class="flex justify-center items-center size-10 border border-blue transition-colors duration-150 hover:text-white hover:bg-blue">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            <button type="button" class="flex justify-center items-center size-10 border border-blue transition-colors duration-150 hover:text-white hover:bg-blue">
                                <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </button>
                            <a href="#" class="flex items-center h-8 font-bold text-white bg-green-400 px-4 border border-green-400 transition-colors duration-150 hover:text-green-400 hover:bg-white sm:h-10 sm:px-5">
                                Predplatné
                            </a>
                        </div>

                        <button type="button" class="flex justify-center items-center size-6 text-blue transition-colors duration-150 hover:text-green-400 xl:hidden">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-12 mt-14 sm:mt-18 lg:py-20">
        <div class="max-w-6xl w-full px-6 mx-auto">
            <div class="single-post-content">
                {!! $content !!}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-white bg-blue-900">
        <div class="max-w-7xl w-full px-6 mx-auto">
            <div class="py-12 border-b border-gray-600 lg:py-20">
                <div class="flex flex-col gap-12 lg:flex-row lg:justify-between lg:gap-28">
                    <ul class="grid gap-8 lg:grid-cols-4 lg:gap-12">
                        <li>
                            <div class="font-bold text-xl mb-4">Movie Context</div>
                        </li>

                        <li class="lg:hidden">
                            <button type="button" class="flex justify-between items-center w-full">
                                <span class="font-bold lg:text-lg">O nás</span>
                                <svg class="size-4 fill-current transform transition-transform duration-150">
                                    <use href="#chevron-down"></use>
                                </svg>
                            </button>
                        </li>

                        <li class="hidden space-y-6 lg:block">
                            <h3 class="text-lg font-bold">O nás</h3>
                            <ul class="space-y-4">
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Predplatné</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Pre študentov</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Podpora</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Kontakt</a>
                                </li>
                            </ul>
                        </li>

                        <li class="lg:hidden">
                            <button type="button" class="flex justify-between items-center w-full">
                                <span class="font-bold lg:text-lg">Spolupráca</span>
                                <svg class="size-4 fill-current transform transition-transform duration-150">
                                    <use href="#chevron-down"></use>
                                </svg>
                            </button>
                        </li>

                        <li class="hidden space-y-6 lg:block">
                            <h3 class="text-lg font-bold">Spolupráca</h3>
                            <ul class="space-y-4">
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Inzercia a spolupráce</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Tiráž</a>
                                </li>
                                <li>
                                    <a href="#" class="text-sm transition-colors duration-150 hover:text-green-400">Copyright a citovanie</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <div class="max-w-full md:max-w-[420px]">
                        <h3 class="text-lg font-bold pb-4">Prihlásiť</h3>
                        <p class="pb-6">Prihláste sa na odber bezplatného týždenného newslettra Movie Context.</p>
                        <div class="flex flex-col w-full space-y-2">
                            <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                                <input type="email" name="newsletter_email" class="w-full text-white bg-transparent p-3 border-[1.5px] border-gray-400 transition-all duration-150 placeholder:text-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-green-400/40 focus:ring-offset-2 focus:ring-offset-blue-900" placeholder="Zadajte svoj e-mail">
                                <a href="#" class="font-bold whitespace-nowrap px-6 py-3 border-[1.5px] border-gray-400 text-white transition-all duration-150 hover:text-white hover:bg-green-400 hover:border-green-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-green-400/40 focus:ring-offset-2 focus:ring-offset-blue-900">Prihlásiť sa</a>
                            </div>
                            <p class="text-sm">Prihlásením potvrdzujem, že som sa oboznámil s pravidlami ochrany osobných údajov.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center gap-6 py-8 lg:flex-row lg:justify-between">
                <div class="flex flex-col items-center gap-6 lg:flex-row">
                    <p class="text-sm">© Copyright {{ date('Y') }} | Movie Context.</p>
                    <ul class="flex flex-col items-center gap-2 sm:flex-row sm:gap-6">
                        <li>
                            <a href="{{ route('terms') }}" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Obchodné podmienky</a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Ochrana osobných údajov</a>
                        </li>
                        <li>
                            <a href="{{ route('cookies') }}" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Cookies</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @livewireScripts
    @stack('scripts')
</body>
</html>
