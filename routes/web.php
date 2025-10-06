<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    $articles = \App\Models\Article::with(['author', 'tags', 'metadata'])
        ->published()
        ->orderBy('published_at', 'desc')
        ->limit(6)
        ->get();

    return view('pages.home', compact('articles'));
})->name('home');

Route::get('/article/{slug}', function ($slug) {
    $article = \App\Models\Article::with(['author', 'tags', 'metadata'])
        ->where('slug', $slug)
        ->published()
        ->firstOrFail();

    // Increment view count
    $article->increment('view_count');

    return view('pages.article', compact('article'));
})->name('article.show');

Route::get('/autor/{slug}', [\App\Http\Controllers\AuthorController::class, 'show'])->name('author.show');

Route::get('/kategoria/{slug}', [\App\Http\Controllers\TagController::class, 'show'])->name('tag.show');

Route::get('/cookies', [\App\Http\Controllers\PageController::class, 'cookies'])->name('cookies');
Route::get('/ochrana-osobnych-udajov', [\App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/obchodne-podmienky', [\App\Http\Controllers\PageController::class, 'terms'])->name('terms');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';
