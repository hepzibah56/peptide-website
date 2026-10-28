<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::query()->when($request->filled('q'), function ($q) use ($request) {
            $term = $request->string('q');
            $q->where(function ($w) use ($term) {
                $w->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            });
        });

        $products = $query->latest()->paginate(50);

        return response()->view('admin.products.index', compact('products'));
    }

    public function create(): Response
    {
        return response()->view('admin.products.create');
    }

    public function store(ProductRequest $request): Response
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['name']));

        $product = Product::create($data);

        return redirect()->route('admin.products.edit', $product)->with('success', 'created');
    }

    public function edit(Product $product): Response
    {
        return response()->view('admin.products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): Response
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['name']), $product->id);

        $product->update($data);

        return back()->with('success', 'updated');
    }

    public function destroy(Product $product): Response
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'deleted');
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($slug);
        $base = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}