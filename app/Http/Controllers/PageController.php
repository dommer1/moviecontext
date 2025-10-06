<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function cookies()
    {
        return view('pages.cookies', [
            'title' => 'Poučenie o spracúvaní súborov cookies',
        ]);
    }

    public function privacy()
    {
        return view('pages.privacy', [
            'title' => 'Ochrana osobných údajov',
        ]);
    }

    public function terms()
    {
        return view('pages.terms', [
            'title' => 'Obchodné podmienky',
        ]);
    }
}
