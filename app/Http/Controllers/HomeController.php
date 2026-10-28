<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $featured = Product::active()
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Product::active()->latest()->take(8)->get();
        }

        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort')->get();

        return view('home', compact('featured', 'categories'));
    }
}