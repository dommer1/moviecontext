<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Movie Context - Najnovšie filmové novinky a recenzie</title>
    <meta name="description" content="Najnovšie filmové novinky, recenzie a správy z filmového sveta">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Movie Context - Najnovšie filmové novinky a recenzie">
    <meta property="og:description" content="Najnovšie filmové novinky, recenzie a správy z filmového sveta">
    <meta property="og:image" content="{{ asset('images/og-homepage.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Movie Context">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Movie Context - Najnovšie filmové novinky a recenzie">
    <meta property="twitter:description" content="Najnovšie filmové novinky, recenzie a správy z filmového sveta">
    <meta property="twitter:image" content="{{ asset('images/og-homepage.jpg') }}">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Movie Context",
        "description": "Najnovšie filmové novinky, recenzie a správy z filmového sveta",
        "url": "{{ url('/') }}",
        "publisher": {
            "@type": "Organization",
            "name": "Movie Context",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo.png') }}",
                "width": 200,
                "height": 60
            }
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ url('/search?q={search_term_string}') }}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }
        .article-grid { display: grid; gap: 2rem; grid-template-columns: 1fr 300px; }
        .article-card { position: relative; }
        .article-meta { display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; }
        .article-meta time { font-size: 0.875rem; font-weight: 600; }
        .article-meta time span { color: #1f2937; }
        .article-title { font-size: 1.5rem; line-height: 1.3; margin-bottom: 1rem; font-weight: 700; }
        .article-title a { color: #1f2937; text-decoration: none; transition: color 0.15s; }
        .article-title a:hover { color: #16a34a; }
        .article-excerpt { padding-bottom: 1.5rem; font-size: 0.875rem; }
        .article-authors { display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
        .author-item { display: flex; align-items: center; gap: 1rem; }
        .author-avatar { width: 2.5rem; height: 2.5rem; border-radius: 50%; overflow: hidden; flex-shrink: 0; }
        .author-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .author-name { font-size: 0.875rem; font-weight: 700; line-height: 1; transition: color 0.15s; }
        .author-name a { color: #1f2937; text-decoration: none; }
        .author-name a:hover { color: #16a34a; }
        .category-badge { display: inline-block; font-size: 0.875rem; font-weight: 700; padding: 0.125rem 0.5rem; background: #dcfce7; color: #166534; transition: all 0.15s; }
        .category-badge:hover { background: #16a34a; color: white; }
        .sidebar-section { background: #e5e7eb; padding: 1.5rem; margin-bottom: 2rem; }
        .sidebar-title { font-size: 1.5rem; font-weight: 700; line-height: 1.3; padding-bottom: 1.5rem; }
        .popular-item { display: flex; gap: 1rem; padding-bottom: 1.5rem; margin-bottom: 1.5rem; border-bottom: 1.5px solid #3b82f6; }
        .popular-item:last-child { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
        .popular-number { font-size: 2.25rem; font-weight: 700; line-height: 1; }
        .popular-title { font-weight: 700; transition: color 0.15s; }
        .popular-title a { color: #1f2937; text-decoration: none; }
        .popular-title a:hover { color: #16a34a; }
        .editor-pick { padding-bottom: 1.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid #3b82f6; }
        .editor-pick:last-child { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
        .editor-pick a { font-weight: 700; color: #1f2937; text-decoration: none; transition: color 0.15s; }
        .editor-pick a:hover { color: #16a34a; }
        .date-badge { font-size: 0.75rem; font-weight: 700; color: white; background: #3b82f6; padding: 0.125rem 0.5rem; }
        .ad-banner { width: 100%; height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; margin: 1.5rem 0; }
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
        <section>
            <div class="max-w-6xl w-full px-6 mx-auto">
                <div class="grid gap-6 grid-cols-1 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        @foreach($articles as $index => $article)
                            @if($index === 0)
                                <x-article-card :article="$article" layout="hero" :show-excerpt="true" />
                            @else
                                <x-article-card :article="$article" />
                            @endif
                        @endforeach

                        <!-- Ad Banner -->
                        <div class="ad-banner">
                            <span class="text-gray-500">Reklama</span>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6 lg:col-start-3 lg:col-end-4">
                        <!-- Rýchly kontext -->
                        <aside>
                            <div class="bg-gray-200 px-6 py-8">
                                <a href="#"><h2 class="text-2xl leading-snug font-bold pb-6 lg:text-3xl">Rýchly kontext</h2></a>

                                <ul class="pb-6">
                                    @foreach($articles->take(4) as $article)
                                    <li class="space-y-2 pb-6 mb-6 border-b-[1.5px] border-blue last:pb-0 last:mb-0 last:border-none">
                                        <span class="text-xs text-white bg-blue px-2 py-1">
                                            <span class="font-bold">{{ $article->published_at->format('d. m. Y') }}</span> {{ $article->published_at->format('H:i') }}
                                        </span>

                                        <h3>
                                            <a href="{{ route('article.show', $article->slug) }}" class="inline-block text-base leading-snug font-bold transition-colors duration-150 hover:text-green-400">
                                                {{ $article->title }}
                                            </a>
                                        </h3>

                                        <div class="text-sm">
                                            <p>{{ $article->excerpt }}</p>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>

                                <a href="#" class="text-sm lg:text-base pe-3 py-3 font-bold underline text-blue mt-4 transition-colors duration-150 hover:text-green-400">
                                    Viac správ
                                </a>
                            </div>

                            <svg viewBox="0 0 45 47" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 -ml-[0.4375rem]">
                                <rect x="5.59015" width="22.3443" height="21.5738" fill="#E9E9EC"></rect>
                                <path d="M14.0459 0V47H0.967163V0H14.0459ZM43.3442 0L10.7297 41.2213L7.90159 38.9098L10.7297 23.8482V22.6512L27.1247 0H43.3442ZM28.3196 47L15.6065 25.4262L27.1247 18.1066L44.5 47H28.3196Z" fill="#292D42"></path>
                            </svg>
                        </aside>

                        <!-- Najčítanejšie -->
                        <aside>
                            <div class="bg-gray-200 px-6 py-8">
                                <h2 class="text-2xl leading-tight font-bold pb-4 lg:text-3xl">Najčítanejšie</h2>

                                <ul class="flex gap-2 pb-6">
                                    <li>
                                        <button class="text-sm font-bold whitespace-nowrap text-white bg-blue px-2 py-1 transition-colors duration-150 hover:bg-green-400 bg-green-400">
                                            24 hod
                                        </button>
                                    </li>
                                    <li>
                                        <button class="text-sm font-bold whitespace-nowrap text-white bg-blue px-2 py-1 transition-colors duration-150 hover:bg-green-400">
                                            7 dní
                                        </button>
                                    </li>
                                    <li>
                                        <button class="text-sm font-bold whitespace-nowrap text-white bg-blue px-2 py-1 transition-colors duration-150 hover:bg-green-400">
                                            30 dní
                                        </button>
                                    </li>
                                </ul>

                                <ul>
                                    @foreach($articles->take(6) as $index => $article)
                                    <li class="flex items-center gap-x-4 pb-6 mb-6 border-b border-blue last:pb-0 last:mb-0 last:border-none">
                                        <span class="text-4xl font-bold leading-none">{{ $index + 1 }}</span>
                                        <h3>
                                            <a href="{{ route('article.show', $article->slug) }}" class="inline-block font-bold transition-colors duration-150 hover:text-green-400">
                                                {{ $article->title }}
                                            </a>
                                        </h3>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <svg viewBox="0 0 45 47" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 -ml-[0.4375rem]">
                                <rect x="5.59015" width="22.3443" height="21.5738" fill="#E9E9EC"></rect>
                                <path d="M14.0459 0V47H0.967163V0H14.0459ZM43.3442 0L10.7297 41.2213L7.90159 38.9098L10.7297 23.8482V22.6512L27.1247 0H43.3442ZM28.3196 47L15.6065 25.4262L27.1247 18.1066L44.5 47H28.3196Z" fill="#292D42"></path>
                            </svg>
                        </aside>
                    </div>
                </div>
            </div>
        </section>
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
                            <a href="#" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Obchodné podmienky</a>
                        </li>
                        <li>
                            <a href="#" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Ochrana osobných údajov</a>
                        </li>
                        <li>
                            <a href="#" class="text-sm underline text-gray-200 transition-colors duration-150 hover:text-green-400">Cookies</a>
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
