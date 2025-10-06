@extends('layouts.app')

@section('meta')
    <!-- Schema.org JSON-LD -->
    @php
        $jsonLd = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => $tag->name,
            "description" => $tag->description ?: 'Články v kategórii ' . $tag->name . ' na Movie Context',
            "url" => url()->current(),
            "mainEntity" => [
                "@type" => "ItemList",
                "numberOfItems" => $articles->total(),
                "itemListElement" => $articles->map(function ($article, $index) {
                    return [
                        "@type" => "ListItem",
                        "position" => $index + 1,
                        "url" => route('article.show', $article->slug),
                        "name" => $article->title
                    ];
                })->values()->all()
            ],
            "breadcrumb" => [
                "@type" => "BreadcrumbList",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "Domov",
                        "item" => url('/')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => $tag->name,
                        "item" => url()->current()
                    ]
                ]
            ]
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endsection

@section('content')

    <!-- Main Content -->
    <main class="py-12 mt-14 sm:mt-18 lg:py-20">
        <section>
            <div class="max-w-6xl w-full px-6 mx-auto">
                <div class="grid gap-12 grid-cols-1 lg:grid-cols-3 lg:gap-6">
                    <div class="space-y-12 lg:col-span-2">
                        <div class="space-y-6">
                            <div class="relative">
                                <div class="flex items-center justify-start gap-4 flex-row">
                                    <h1 class="text-3xl font-bold leading-none sm:text-4xl visited:text-white">{{ $tag->name }}</h1>
                                </div>

                                @if($tag->description)
                                    <div class="text-lg leading-relaxed text-gray-600 max-w-4xl">
                                        <p>{{ $tag->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-6">
                            <ul class="space-y-6 js-posts-container">
                                    @foreach($articles as $index => $article)
                                        @if($index === 0)
                                            <li><x-article-card :article="$article" layout="featured" /></li>
                                        @else
                                            <li><x-article-card :article="$article" /></li>
                                        @endif
                                    @endforeach
                            </ul>

                            <!-- Pagination -->
                            {{ $articles->links() }}
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6 lg:col-start-3 lg:col-end-4">
                        <aside>
                            <div class="bg-gray-200 px-6 py-8">
                                <a href="#"><h2 class="text-2xl leading-snug font-bold pb-6 lg:text-3xl">Rýchly kontext</h2></a>

                                <ul class="pb-6">
                                    <li class="space-y-2 pb-6 mb-6 border-b-[1.5px] border-blue last:pb-0 last:mb-0 last:border-none">
                                        <span class="text-xs text-white bg-blue px-2 py-1">
                                            <span class="font-bold">{{ now()->subDays(3)->format('d. m. Y') }}</span> {{ now()->subDays(3)->format('H:i') }}
                                        </span>

                                        <h3>
                                            <a href="#" class="inline-block text-base leading-snug font-bold transition-colors duration-150 hover:text-green-400">
                                                Podrobnosti o nezákonnom odvolaní predsedu súdu
                                            </a>
                                        </h3>

                                        <div class="text-sm"><p>Roman Fitt st. si ako predseda súdu plnil svoje povinnosti svedomito, uvádza sa v rozsudku.</p></div>
                                    </li>
                                    <li class="space-y-2 pb-6 mb-6 border-b-[1.5px] border-blue last:pb-0 last:mb-0 last:border-none">
                                        <span class="text-xs text-white bg-blue px-2 py-1">
                                            <span class="font-bold">{{ now()->subDays(5)->format('d. m. Y') }}</span> {{ now()->subDays(5)->format('H:i') }}
                                        </span>

                                        <h3>
                                            <a href="#" class="inline-block text-base leading-snug font-bold transition-colors duration-150 hover:text-green-400">
                                                M. Koliková pochybila pri odvolaní predsedu súdu
                                            </a>
                                        </h3>

                                        <div class="text-sm"><p>Odôvodnenie rozsudku bude známe v najbližších dňoch.</p></div>
                                    </li>
                                    <li class="space-y-2 pb-6 mb-6 border-b-[1.5px] border-blue last:pb-0 last:mb-0 last:border-none">
                                        <span class="text-xs text-white bg-blue px-2 py-1">
                                            <span class="font-bold">{{ now()->subDays(7)->format('d. m. Y') }}</span> {{ now()->subDays(7)->format('H:i') }}
                                        </span>

                                        <h3>
                                            <a href="#" class="inline-block text-base leading-snug font-bold transition-colors duration-150 hover:text-green-400">
                                                Disciplinárny trest mu zrušil Ústavný súd
                                            </a>
                                        </h3>

                                        <div class="text-sm"><p>Analýza medializácie disciplinárnej kauzy sudcu Špecializovaného trestného súdu Michala Trubana.</p></div>
                                    </li>
                                    <li class="space-y-2 pb-6 mb-6 border-b-[1.5px] border-blue last:pb-0 last:mb-0 last:border-none">
                                        <span class="text-xs text-white bg-blue px-2 py-1">
                                            <span class="font-bold">{{ now()->subDays(9)->format('d. m. Y') }}</span> {{ now()->subDays(9)->format('H:i') }}
                                        </span>

                                        <h3>
                                            <a href="#" class="inline-block text-base leading-snug font-bold transition-colors duration-150 hover:text-green-400">
                                                ESĽP sa zaoberá slovenskou protikorupčnou kauzou
                                            </a>
                                        </h3>

                                        <div class="text-sm"><p>V utorok boli publikované oznámenia viacerých sťažností.</p></div>
                                    </li>
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

                        <!-- Ad Banner -->
                        <div class="w-full flex adbanner" style="height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                            <span class="text-gray-500">Reklama</span>
                        </div>
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
@endsection
