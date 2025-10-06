<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $author->name }} - Movie Context</title>
    <meta name="description" content="{{ $author->bio }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }
        .category-badge { display: inline-block; font-size: 0.875rem; font-weight: 700; padding: 0.125rem 0.5rem; background: #dcfce7; color: #166534; transition: all 0.15s; }
        .category-badge:hover { background: #16a34a; color: white; }
        .article-meta { display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; }
        .article-meta time { font-size: 0.875rem; font-weight: 600; }
        .article-meta time span { color: #1f2937; }
        .article-title { font-size: 1.125rem; line-height: 1.4; margin-bottom: 0.5rem; font-weight: 700; }
        .article-title a { color: #1f2937; text-decoration: none; transition: color 0.15s; }
        .article-title a:hover { color: #16a34a; }
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
    <main class="space-y-20 mt-14 sm:mt-18 lg:pt-20">
        <section>
            <div class="max-w-6xl w-full px-6 mx-auto">
                <div class="space-y-12 sm:space-y-20">
                    <section class="space-y-12">
                        <div class="flex flex-col items-center gap-8 sm:flex-row">
                            <img src="https://via.placeholder.com/128x128?text={{ substr($author->name, 0, 2) }}" alt="{{ $author->name }}" class="shrink-0 size-32 rounded-full object-cover">

                            <div class="flex flex-col items-center gap-4 sm:items-start">
                                <h1 class="text-3xl font-bold leading-none sm:text-4xl">{{ $author->name }}</h1>

                                <ul class="flex gap-x-2">
                                    <li>
                                        <span class="category-badge">{{ ucfirst($author->specialization) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div x-data="{ showMore: false, shortBio: '{{ Str::limit(strip_tags($author->bio), 400) }}', fullBio: '{{ $author->bio }}' }" class="space-y-4">
                            <div class="leading-relaxed sm:text-lg text-box-content" x-html="showMore ? fullBio : shortBio"></div>

                            <button type="button" @click="showMore = !showMore" class="text-sm font-bold underline text-blue mt-4 transition-colors duration-150 hover:text-green-400" x-text="showMore ? 'Čítať menej' : 'Čítať viac'">Čítať viac</button>
                        </div>
                    </section>

                    <section class="space-y-6 sm:space-y-10">
                        <h2 class="text-3xl font-bold leading-none sm:text-4xl">Články autora</h2>

                        <div class="grid gap-12 grid-cols-1 lg:grid-cols-3 lg:gap-6">
                            <div class="lg:col-span-2">
                                <div class="space-y-6 js-posts-container">
                                    @foreach($articles as $article)
                                    <article class="relative flex flex-col gap-6 p-6 -mx-6 bg-gray-100 after:absolute after:inset-x-6 after:bottom-0 after:h-[1.5px] after:bg-gray-200 sm:border sm:border-gray-200 sm:after:hidden xs:flex-row xs:items-stretch sm:p-0 sm:mx-0">
                                        <div class="grow space-y-4 sm:p-8">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                                                <x-tag-list :tags="$article->tags" />
                                                <span class="text-sm">
                                                    <span class="font-bold">{{ $article->published_at->format('d. m. Y') }}</span> {{ $article->published_at->format('H:i') }}
                                                </span>
                                            </div>

                                            <h2>
                                                <a href="{{ route('article.show', $article->slug) }}" class="inline-block text-lg leading-snug font-bold transition-colors duration-150 hover:text-green-400 sm:text-xl">
                                                    {{ $article->title }}
                                                </a>
                                            </h2>

                                            <x-author-link :author="$author" class="inline-block text-sm font-bold transition-colors duration-150 hover:text-green-400" />
                                        </div>

                                        <a href="{{ route('article.show', $article->slug) }}" class="flex shrink-0 h-full overflow-hidden xs:w-40 sm:w-72 my-auto">
                                            <img src="https://via.placeholder.com/400x225?text={{ urlencode($article->title) }}" class="attachment-large size-large w-full h-full object-cover" alt="">
                                        </a>
                                    </article>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                {{ $articles->links() }}
                            </div>

                            <!-- Sidebar -->
                            <div class="space-y-6 lg:col-start-3 lg:col-end-4">
                                <aside x-data="{ activeTab: 0 }">
                                    <div class="bg-gray-200 px-6 py-8">
                                        <h2 class="text-2xl leading-tight font-bold pb-4 lg:text-3xl">Najčítanejšie články autora</h2>

                                        <ul class="flex gap-2 pb-6">
                                            <li>
                                                <button type="button" @click="activeTab = 0" class="text-sm font-bold whitespace-nowrap text-white bg-blue px-2 py-1 transition-colors duration-150 hover:bg-green-400 bg-green-400" :class="{ 'bg-green-400': activeTab == 0 }">
                                                    7 dní
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" @click="activeTab = 1" class="text-sm font-bold whitespace-nowrap text-white bg-blue px-2 py-1 transition-colors duration-150 hover:bg-green-400" :class="{ 'bg-green-400': activeTab == 1 }">
                                                    30 dní
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" @click="activeTab = 2" class="text-sm font-bold whitespace-nowrap text-white bg-blue px-2 py-1 transition-colors duration-150 hover:bg-green-400" :class="{ 'bg-green-400': activeTab == 2 }">
                                                    365 dní
                                                </button>
                                            </li>
                                        </ul>

                                        <ul x-show="activeTab == 0">
                                            @php
                                                $popularArticles = $author->articles()
                                                    ->published()
                                                    ->where('published_at', '>=', now()->subDays(7))
                                                    ->orderBy('view_count', 'desc')
                                                    ->limit(6)
                                                    ->get();
                                            @endphp
                                            @foreach($popularArticles as $index => $article)
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

                                        <ul x-show="activeTab == 1" style="display: none;">
                                            @php
                                                $popularArticles = $author->articles()
                                                    ->published()
                                                    ->where('published_at', '>=', now()->subDays(30))
                                                    ->orderBy('view_count', 'desc')
                                                    ->limit(6)
                                                    ->get();
                                            @endphp
                                            @foreach($popularArticles as $index => $article)
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

                                        <ul x-show="activeTab == 2" style="display: none;">
                                            @php
                                                $popularArticles = $author->articles()
                                                    ->published()
                                                    ->where('published_at', '>=', now()->subDays(365))
                                                    ->orderBy('view_count', 'desc')
                                                    ->limit(6)
                                                    ->get();
                                            @endphp
                                            @foreach($popularArticles as $index => $article)
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

                                <!-- Ad Banner -->
                                <div class="w-full flex adbanner" style="height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-gray-500">Reklama</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="bg-green-100 py-16 lg:py-28">
            <div class="max-w-6xl w-full px-6 mx-auto">
                <div class="grid gap-12 sm:gap-20 lg:grid-cols-2">
                    <div class="space-y-6">
                        <h2 class="text-2xl leading-tight font-bold sm:text-3xl lg:text-4xl">Prihláste sa na <br>odber newslettra</h2>

                        <div x-data="{ email: '' }" class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                            <input x-model="email" type="email" name="newsletter_email" class="w-full text-blue bg-transparent p-3 border-[1.5px] border-blue transition-all duration-150 placeholder:text-blue/80 focus:outline-none focus:border-blue focus:ring-2 focus:ring-green-400/40 focus:ring-offset-2 focus:ring-offset-green-100" placeholder="Zadajte svoj email">
                            <a :href="'/registracia/bezplatny-odber/?email=' + email" class="font-bold whitespace-nowrap text-white bg-blue px-6 py-3 border border-blue transition-all duration-150 hover:text-gray-200 hover:bg-green-400 hover:border-green-400 focus:outline-none focus:border-gray-200 focus:ring-2 focus:ring-green-400/40 focus:ring-offset-2 focus:ring-offset-blue">Prihlásiť sa</a>
                        </div>
                    </div>

                    <div>
                        <p class="pb-6">Získajte najnovšie články od tohto autora priamo do vašej schránky. Prihláste sa na odber a buďte vždy informovaní!</p>

                        <ul class="space-y-6 pl-5">
                            <li class="list-disc"><strong>Rýchly kontext</strong> do vášho e-mailu</li>
                            <li class="list-disc">Prístup k <strong>špeciálnym vydaniam článkov</strong></li>
                            <li class="list-disc">Byť medzi <strong>prvými</strong> informovaný ohľadom <strong>noviniek</strong></li>
                        </ul>
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
