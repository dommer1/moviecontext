<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $article->seo_title ?? $article->title }} - Movie Context</title>
    <meta name="description" content="{{ $article->seo_description ?? $article->excerpt }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $article->seo_title ?? $article->title }}">
    <meta property="og:description" content="{{ $article->seo_description ?? $article->excerpt }}">
    <meta property="og:image" content="{{ $article->featured_image_path ? asset('storage/' . $article->featured_image_path) : asset('images/default-movie.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Movie Context">
    <meta property="article:published_time" content="{{ $article->published_at?->toISOString() }}">
    <meta property="article:modified_time" content="{{ $article->updated_at->toISOString() }}">
    <meta property="article:author" content="{{ $article->author->name }}">
    @foreach($article->tags as $tag)
    <meta property="article:tag" content="{{ $tag->name }}">
    @endforeach

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $article->seo_title ?? $article->title }}">
    <meta property="twitter:description" content="{{ $article->seo_description ?? $article->excerpt }}">
    <meta property="twitter:image" content="{{ $article->featured_image_path ? asset('storage/' . $article->featured_image_path) : asset('images/default-movie.jpg') }}">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ $article->seo_title ?? $article->title }}",
        "description": "{{ $article->seo_description ?? $article->excerpt }}",
        "image": {
            "@type": "ImageObject",
            "url": "{{ $article->featured_image_path ? asset('storage/' . $article->featured_image_path) : asset('images/default-movie.jpg') }}",
            "width": 1200,
            "height": 630
        },
        "datePublished": "{{ $article->published_at?->toISOString() }}",
        "dateModified": "{{ $article->updated_at->toISOString() }}",
        "author": {
            "@type": "Person",
            "name": "{{ $article->author->name }}",
            "url": "{{ route('author.show', $article->author->slug) }}"
        },
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
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}"
        },
        "keywords": "{{ $article->tags->pluck('name')->join(', ') }}",
        "articleSection": "Filmové novinky",
        "wordCount": "{{ str_word_count($article->content) }}",
        "timeRequired": "PT{{ $article->reading_time }}M",
        "url": "{{ url()->current() }}",
        "breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Domov",
                    "item": "{{ url('/') }}"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "{{ $article->author->name }}",
                    "item": "{{ route('author.show', $article->author->slug) }}"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "{{ $article->title }}",
                    "item": "{{ url()->current() }}"
                }
            ]
        }
        @if($article->metadata && $article->metadata->imdb_id)
        ,"sameAs": [
            "{{ $article->metadata->imdb_id ? 'https://www.imdb.com/title/' . $article->metadata->imdb_id : null }}",
            "{{ $article->metadata->csfd_url }}"
        ]
        @endif
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
        .category-badge { display: inline-block; font-size: 0.875rem; font-weight: 700; padding: 0.125rem 0.5rem; background: #dcfce7; color: #166534; transition: all 0.15s; }
        .category-badge:hover { background: #16a34a; color: white; }
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

    <!-- Article Content -->
    <main class="py-12 mt-14 sm:mt-18 lg:py-20">
        <section x-data="{ showShortContext: false }">
            <div class="relative max-w-6xl w-full px-6 mx-auto">
                <div class="grid gap-12 grid-cols-1 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <div class="flex items-center pb-6">
                            <span class="text-sm after:content-['•'] after:inline-block after:px-2"><span class="font-bold">{{ $article->published_at->format('d. m. Y') }}</span> {{ $article->published_at->format('H:i') }}</span>
                            <span class="text-sm">{{ $article->reading_time }} min čítania</span>
                        </div>

                                                <ul class="flex items-center flex-wrap gap-2 pb-6">
                                                            <ul class="flex flex-wrap items-center gap-2">

            <li>
            <a href="#" class="category-badge">
                {{ $article->tags->first()?->name ?? 'Článok' }}
            </a>
        </li>
    </ul>
                                                            <ul class="flex flex-wrap gap-2">

            <li>
            <x-tag-list :tags="$article->tags" />
        </li>
    </ul>                                                    </ul>

                        <h1 class="text-3xl leading-tight font-bold pb-6 lg:text-4xl">
                                                    {{ $article->title }}
                                            </h1>

                                                <div class="text-lg leading-relaxed pb-12 sm:text-xl sm:leading-relaxed sm:pb-16"><p>{{ $article->excerpt }}</p>
</div>

                        @if($article->metadata && $article->metadata->streaming_platforms)
                            <div class="pb-8">
                                <x-streaming-platforms :platforms="$article->metadata->streaming_platforms" />
                            </div>
                        @endif

                        <figure class="pb-12 sm:pb-16">
                                                                                    <img width="1024" height="576" src="https://via.placeholder.com/1024x576?text={{ urlencode($article->title) }}" class="attachment-large size-large w-full h-full object-cover" alt="" decoding="async" fetchpriority="high">
                                                                                        <figcaption class="text-xs text-gray-300 pl-2 mt-2 border-l-2 border-gray-300">
                                    Newsletter {{ $article->author->name }}. (Foto: Kontext Media)
                                </figcaption>
                                                                        </figure>

                        <div class="space-y-4 pb-8">
                                                    <ul class="grid gap-6 sm:grid-cols-2 md:grid-cols-3">
                                                                    <li class="flex items-center gap-4">
                                                                                    <a href="{{ route('author.show', $article->author->slug) }}" class="block shrink-0">
                                                <div class="w-14 h-14 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-bold">{{ substr($article->author->name, 0, 2) }}</span>
                                                </div>
                                            </a>

                                        <div class="space-y-1.5">
                                            <a href="{{ route('author.show', $article->author->slug) }}" class="block font-bold leading-none transition-colors duration-150 hover:text-green-400">{{ $article->author->name }}</a>
                                        </div>
                                    </li>
                                                            </ul>

                            <ul class="flex flex-wrap gap-2 sm:gap-4">
                                <li>
                                    <div x-data="{
                                            popoverOpen: false,
                                            popoverArrow: true,
                                            popoverPosition: 'bottom',
                                            popoverHeight: 0,
                                            popoverOffset: 8,
                                            popoverHeightCalculate() {
                                                this.$refs.popover.classList.add('invisible');
                                                this.popoverOpen=true;
                                                let that=this;
                                                $nextTick(function(){
                                                    that.popoverHeight = that.$refs.popover.offsetHeight;
                                                    that.popoverOpen=false;
                                                    that.$refs.popover.classList.remove('invisible');
                                                    that.$refs.popoverInner.setAttribute('x-transition', '');
                                                    that.popoverPositionCalculate();
                                                });
                                            },
                                            popoverPositionCalculate(){
                                                if(window.innerHeight &lt; (this.$refs.popoverButton.getBoundingClientRect().top + this.$refs.popoverButton.offsetHeight + this.popoverOffset + this.popoverHeight)){
                                                    this.popoverPosition = 'top';
                                                } else {
                                                    this.popoverPosition = 'bottom';
                                                }
                                            }
                                        }" x-init="
                                            that = this;
                                            window.addEventListener('resize', function(){
                                                popoverPositionCalculate();
                                            });" class="relative">

                                        <button x-ref="popoverButton" @click="popoverOpen=!popoverOpen" class="inline-flex items-center gap-x-2 whitespace-nowrap text-blue bg-gray-200 px-2 py-1 transition-colors duration-150 hover:text-white hover:bg-green-400">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                            </svg>
                                            <span class="text-sm font-bold">Zdielať článok</span>
                                        </button>

                                        <div x-ref="popover" x-show="popoverOpen" x-init="setTimeout(function(){ popoverHeightCalculate(); }, 100);" x-trap.inert="popoverOpen" @click.away="popoverOpen=false;" @keydown.escape.window="popoverOpen=false" :class="{ 'top-0 mt-10' : popoverPosition == 'bottom', 'bottom-0 mb-12' : popoverPosition == 'top' }" class="absolute w-[300px] max-w-lg left-0 top-0 mt-10" style="display: none;">
                                            <div x-ref="popoverInner" x-show="popoverOpen" class="w-full p-4 bg-white border shadow-md border-neutral-200/70" x-transition="" style="display: none;">
                                                <div x-show="popoverArrow &amp;&amp; popoverPosition == 'bottom'" class="absolute top-0 inline-block w-5 mt-px overflow-hidden  -translate-y-2.5 left-px"><div class="w-2.5 h-2.5 origin-bottom-left transform rotate-45 bg-white border-t border-l rounded-sm"></div></div>
                                                <div class="grid gap-4">
                                                    <div class="space-y-2">
                                                        <h4 class="font-bold leading-tight">Zdielať článok na sociálnych sieťach</h4>
                                                    </div>
                                                    <ul class="flex flex-col gap-2">
                                                        <li class="flex gap-1 items-center">
                                                            <a href="https://www.linkedin.com/feed/?shareActive=true&amp;text={{ urlencode($article->title) }} - {{ url()->current() }}" class="flex items-center gap-2 group" target="_blank" rel="noopener">
                                                                <div class="flex justify-center items-center size-8 text-blue bg-gray-200 transition-colors duration-150 group-hover:text-white group-hover:bg-green-400">
                                                                    <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8a6 6 0 019 5.916V22l-6-3-6 3V13.916A6 6 0 0116 8z"></path>
                                                                    </svg>
                                                                </div>
                                                                <span>Zdieľať na LinkedIn</span>
                                                            </a>
                                                        </li>
                                                        <li class="flex gap-1 items-center">
                                                            <a href="https://x.com/intent/tweet?url={{ url()->current() }}&amp;text={{ urlencode($article->title) }}" class="flex items-center gap-2 group" target="_blank" rel="noopener">
                                                                <div class="flex justify-center items-center size-8 text-blue bg-gray-200 transition-colors duration-150 group-hover:text-white group-hover:bg-green-400">
                                                                    <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                                                                    </svg>
                                                                </div>
                                                                <span>Zdieľať na X</span>
                                                            </a>
                                                        </li>
                                                        <li class="flex gap-1 items-center">
                                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" class="flex items-center gap-2 group" target="_blank" rel="noopener">
                                                                <div class="flex justify-center items-center size-8 text-blue bg-gray-200 transition-colors duration-150 group-hover:text-white group-hover:bg-green-400">
                                                                    <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                                                                    </svg>
                                                                </div>
                                                                <span>Zdieľať na Facebook</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div x-show="!showShortContext" class="single-post-content">
                            <div class="text-lg leading-relaxed">
                                {!! nl2br(e($article->content)) !!}
                            </div>

                            <!-- Fun Fact Section -->
                            @if($article->metadata && $article->metadata->fun_fact)
                                <div class="mt-8 bg-green-400 p-6 before:absolute before:inset-0 before:gradient-1 before:-translate-y-full sm:p-8 subscription-block">
                                    <h2 class="text-2xl leading-snug font-bold">Tento článok je dostupný po registrácii.</h2>
                                    <p class="lg:text-lg">Bezplatnou registráciou získate prístup k redakciou vybraným článkom a pravidelnému týždennému newsletteru.</p>
                                    <div class="inline-flex gap-2 items-center">
                                        <a href="#" class="inline-block text-sm font-bold text-white bg-blue px-4 py-2 transition-colors duration-150 hover:text-blue hover:bg-white">Zaregistrovať sa</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4 py-8 border-b border-blue">
                            <h3 class="font-bold text-center lg:text-lg lg:text-left">Zdielať článok</h3>

                            <div class="flex flex-col items-center gap-6 lg:flex-row lg:justify-between lg:items-end lg:gap-8">
                                <ul class="flex gap-x-2">
                                    <li>
                                        <a href="mailto:?subject={{ urlencode($article->title) }}&amp;body={{ url()->current() }}" class="flex justify-center items-center size-8 text-blue bg-gray-200 border-none transition-colors duration-150 hover:text-white hover:bg-green-400" target="_blank" rel="noopener">
                                            <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.linkedin.com/feed/?shareActive=true&amp;text={{ urlencode($article->title) }} - {{ url()->current() }}" class="flex justify-center items-center size-8 text-blue bg-gray-200 transition-colors duration-150 hover:text-white hover:bg-green-400" target="_blank" rel="noopener">
                                            <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8a6 6 0 019 5.916V22l-6-3-6 3V13.916A6 6 0 0116 8z"></path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://x.com/intent/tweet?url={{ url()->current() }}&amp;text={{ urlencode($article->title) }}" class="flex justify-center items-center size-8 text-blue bg-gray-200 transition-colors duration-150 hover:text-white hover:bg-green-400" target="_blank" rel="noopener">
                                            <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" class="flex justify-center items-center size-8 text-blue bg-gray-200 transition-colors duration-150 hover:text-white hover:bg-green-400" target="_blank" rel="noopener">
                                            <svg class="size-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                                            </svg>
                                        </a>
                                    </li>
                                </ul>

                                                            <ul class="flex flex-wrap gap-2">

            <li>
            <x-tag-list :tags="$article->tags" />
        </li>
    </ul>                                                    </div>
                        </div>
                    </div>

                    <div class="hidden lg:block lg:col-start-3 lg:col-end-4 lg:space-y-6">
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

                                                    <div class="text-sm"><p>Roman Fitt st. si ako predseda súdu plnil svoje povinnosti svedomito, uvádza sa v rozsudku.</p>
</div>
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

                                                    <div class="text-sm"><p>Odôvodnenie rozsudku bude známe v najbližších dňoch.</p>
</div>
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

                                                    <div class="text-sm"><p>Analýza medializácie disciplinárnej kauzy sudcu Špecializovaného trestného súdu Michala Trubana.</p>
</div>
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

                                                    <div class="text-sm"><p>V utorok boli publikované oznámenia viacerých sťažností.</p>
</div>
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
                    </div>
                </div>
            </div>
        </section>

        <!-- Similar Articles Section -->
        <section class="pt-16 transform translate-x-0 translate-y-0 overflow-x-hidden lg:pt-28">
            <div class="max-w-7xl w-full px-4 mx-auto">
                <div class="space-y-6 sm:space-y-12">
                    <h2 class="text-3xl leading-tight font-bold lg:text-4xl">Mohlo by vás zaujímať</h2>

                    <div class="swiper js-similar-posts-swiper">
                        <ul class="swiper-wrapper">
                            @php
                                $similarArticles = \App\Models\Article::with(['author', 'tags'])
                                    ->published()
                                    ->where('id', '!=', $article->id)
                                    ->limit(8)
                                    ->get();
                            @endphp
                            @foreach($similarArticles as $similarArticle)
                                <li class="swiper-slide">
                                    <article class="space-y-6">
                                        <a href="{{ route('article.show', $similarArticle->slug) }}" class="block h-50 overflow-hidden">
                                            <img src="https://via.placeholder.com/400x225?text={{ urlencode($similarArticle->title) }}" alt="" class="size-full object-cover transition-transform duration-300 hover:scale-105">
                                        </a>

                                        <x-tag-list :tags="$similarArticle->tags" />

                                        <h3>
                                            <a href="{{ route('article.show', $similarArticle->slug) }}" class="text-xl font-bold transition-colors duration-150 hover:text-green-400 sm:text-2xl">
                                                {{ $similarArticle->title }}
                                            </a>
                                        </h3>
                                    </article>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="swiper-pagination js-similar-posts-swiper-pagination"></div>

                        <div class="flex gap-x-4 splide__arrows">
                            <button type="button" class="hidden justify-center items-center size-12 text-blue border border-blue transition-colors duration-150 hover:enabled:text-white hover:enabled:bg-green-400 hover:enabled:border-green-400 disabled:opacity-60 sm:flex js-similar-posts-swiper-button-prev">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>

                            <button type="button" class="hidden justify-center items-center size-12 text-blue border border-blue transition-colors duration-150 hover:enabled:text-white hover:enabled:bg-green-400 hover:enabled:border-green-400 disabled:opacity-60 sm:flex js-similar-posts-swiper-button-next">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
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
    </footer>

    <!-- Scripts -->
    @livewireScripts
    @stack('scripts')
</body>
</html>
