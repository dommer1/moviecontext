<x-filament-panels::page>
    <div class="space-y-6">
        <!-- System Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Published Articles</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $this->getTotalArticles() }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Scraped</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $this->getTotalScrapedArticles() }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Sources</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $this->getActiveSources() }} / {{ $this->getTotalSources() }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Articles Today</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $this->getArticlesToday() }}</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Articles -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Articles</h3>
                <div class="space-y-3">
                    @foreach($this->getRecentArticles() as $article)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-medium text-sm">{{ Str::limit($article->title, 50) }}</p>
                                <p class="text-xs text-gray-500">{{ $article->author->name ?? 'Unknown' }}</p>
                            </div>
                            <div class="text-right text-xs text-gray-500">
                                {{ $article->published_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Scraping Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Scraping Status</h3>
                @php $status = $this->getScrapingStatus() @endphp
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm">Pending:</span>
                        <span class="font-medium">{{ $status['pending'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm">Selected for Generation:</span>
                        <span class="font-medium">{{ $status['selected_for_generation'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm">Processed:</span>
                        <span class="font-medium">{{ $status['processed'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm">Error:</span>
                        <span class="font-medium text-red-600">{{ $status['error'] }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-500">Last scraping: {{ $this->getLastScrapingTime()?->diffForHumans() ?? 'Never' }}</p>
                    <p class="text-sm text-gray-500">Last published: {{ $this->getLastArticlePublished()?->diffForHumans() ?? 'Never' }}</p>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">System Information</h3>
            @php $system = $this->getSystemInfo() @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">PHP Version</p>
                    <p class="font-medium">{{ $system['php_version'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Laravel Version</p>
                    <p class="font-medium">{{ $system['laravel_version'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Database</p>
                    <p class="font-medium">{{ $system['database'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Environment</p>
                    <p class="font-medium">{{ $system['environment'] }}</p>
                </div>
            </div>
        </div>

        <!-- Author Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Author Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($this->getAuthorStats() as $author)
                    <div class="text-center">
                        <p class="font-medium">{{ $author->name }}</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $author->articles_count }}</p>
                        <p class="text-sm text-gray-500">articles published</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-4">
                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.sources.index') }}"
                    color="gray"
                >
                    Manage Sources
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.articles.index') }}"
                    color="gray"
                >
                    Manage Articles
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.authors.index') }}"
                    color="gray"
                >
                    Manage Authors
                </x-filament::button>

                <x-filament::button
                    wire:click="$dispatch('runCommand', { command: 'scrape:sources' })"
                    color="success"
                >
                    Run Scraping
                </x-filament::button>

                <x-filament::button
                    wire:click="$dispatch('runCommand', { command: 'content:select' })"
                    color="success"
                >
                    Select Content
                </x-filament::button>

                <x-filament::button
                    wire:click="$dispatch('runCommand', { command: 'content:generate' })"
                    color="success"
                >
                    Generate Articles
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
