<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::active();

        if ($request->filled('q')) {
            $term = $request->string('q');
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $sort = $request->string('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->latest(),
        };

        $products = $query->paginate(24)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function show(string $slug): View
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->where('category', $product->category)
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }

    public function category(string $category, Request $request): View
    {
        $query = Product::active()->where('category', $category);

        $sort = $request->string('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->latest(),
        };

        $products = $query->paginate(24)->withQueryString();

        return view('products.index', compact('products'))->with('activeCategory', $category);
    }
}