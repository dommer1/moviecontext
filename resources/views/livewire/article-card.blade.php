<article class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
    <div class="aspect-[16/9] bg-gray-200 relative">
        @if($article->featured_image_path)
            <img src="{{ asset('storage/' . $article->featured_image_path) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
        @else
            <img src="https://via.placeholder.com/400x225?text=Film+Image" alt="{{ $article->title }}" class="w-full h-full object-cover">
        @endif

        <!-- Category Badge -->
        <div class="absolute top-4 left-4">
            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                {{ $article->tags->first()?->name ?? 'Článok' }}
            </span>
        </div>
    </div>

    <div class="p-4">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <span class="font-medium text-gray-900">{{ $article->author->name }}</span>
            <span class="mx-2">•</span>
            <time>{{ $article->published_at?->diffForHumans() ?? $article->created_at->diffForHumans() }}</time>
        </div>

        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
            {{ $article->title }}
        </h3>

        <p class="text-gray-600 text-sm line-clamp-2 mb-3">
            {{ $article->excerpt }}
        </p>

        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    {{ $article->view_count }}
                </span>
                @if($article->tags->count() > 0)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        {{ $article->tags->count() }}
                    </span>
                @endif
            </div>
            <a href="{{ route('article.show', $article->slug) }}" class="text-red-600 hover:text-red-700 font-medium text-sm">
                Čítať viac →
            </a>
        </div>
    </div>
</article>
