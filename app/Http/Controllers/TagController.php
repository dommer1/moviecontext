<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    public function show($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $articles = $tag->articles()
            ->published()
            ->with(['author', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('pages.tag', compact('tag', 'articles'));
    }
}
