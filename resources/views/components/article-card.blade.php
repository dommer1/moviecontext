@props(['article', 'layout' => 'default', 'showExcerpt' => false])

@if($layout === 'featured')
    <!-- Featured Article Layout -->
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

            <x-author-link :author="$article->author" class="inline-block text-sm font-bold transition-colors duration-150 hover:text-green-400" />
        </div>

        <a href="{{ route('article.show', $article->slug) }}" class="flex shrink-0 h-full overflow-hidden xs:w-40 sm:w-72 my-auto">
            <img src="https://via.placeholder.com/400x225?text={{ urlencode($article->title) }}" class="attachment-large size-large w-full h-full object-cover" alt="">
        </a>
    </article>
@elseif($layout === 'hero')
    <!-- Hero Layout for Homepage -->
    <article class="relative -mx-6 after:absolute after:inset-x-6 after:bottom-0 after:h-[1.5px] after:bg-gray-200 sm:mx-0 sm:border sm:border-gray-200 sm:after:hidden">
        <a href="{{ route('article.show', $article->slug) }}" class="block h-64 overflow-hidden sm:h-80">
            <img src="https://via.placeholder.com/1024x576?text={{ urlencode($article->title) }}" class="attachment-large size-large w-full h-full object-cover" alt="">
        </a>

        <div class="bg-gray-100 p-6 sm:p-8">
            <div class="flex items-center gap-4 pb-4">
                <x-tag-list :tags="$article->tags" />

                <span class="text-sm">
                    <span class="font-bold">{{ $article->published_at->format('d. m. Y') }}</span> {{ $article->published_at->format('H:i') }}
                </span>
            </div>

            <h2>
                <a href="{{ route('article.show', $article->slug) }}" class="inline-block text-2xl font-bold leading-snug mb-4 transition-colors duration-150 hover:text-green-400 sm:text-3xl">
                    {{ $article->title }}
                </a>
            </h2>

            @if($showExcerpt)
                <div class="pb-6 text-box-content">
                    <p>{{ $article->excerpt }}</p>
                </div>
            @endif

            <ul class="grid gap-6 xs:grid-cols-2 md:grid-cols-3">
                <x-author-block :author="$article->author" size="sm" />
            </ul>
        </div>
    </article>
@else
    <!-- Default Article Layout -->
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

            <x-author-link :author="$article->author" class="inline-block text-sm font-bold transition-colors duration-150 hover:text-green-400" />
        </div>

        <a href="{{ route('article.show', $article->slug) }}" class="flex shrink-0 h-full overflow-hidden xs:w-40 sm:w-72 my-auto">
            <img src="https://via.placeholder.com/400x225?text={{ urlencode($article->title) }}" class="attachment-large size-large w-full h-full object-cover" alt="">
        </a>
    </article>
@endif
