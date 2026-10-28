<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocaleController extends Controller
{
    public function switch(Request $request, string $lang): Response
    {
        if (! in_array($lang, ['nb', 'en'])) {
            abort(404);
        }

        $previous = url()->previous();
        $parsed = parse_url($previous);
        $path = $parsed['path'] ?? '/';

        $segments = array_values(array_filter(explode('/', $path)));
        $segments = array_map('strtolower', $segments);

        if (isset($segments[0]) && in_array($segments[0], ['nb', 'en'])) {
            array_splice($segments, 0, 1, [$lang]);
        } else {
            array_unshift($segments, $lang);
        }

        $newPath = '/'.implode('/', $segments);

        return redirect()->to($newPath);
    }
}