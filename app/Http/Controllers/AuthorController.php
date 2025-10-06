<?php

namespace App\Http\Controllers;

use App\Models\Author;

class AuthorController extends Controller
{
    public function show($slug)
    {
        $author = Author::where('slug', $slug)->firstOrFail();

        $articles = $author->articles()
            ->published()
            ->with(['tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('pages.author', compact('author', 'articles'));
    }
}
