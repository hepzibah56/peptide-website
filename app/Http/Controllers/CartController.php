<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index(Request $request): Response
    {
        $items = $this->cart->items();

        return response()->view('cart.index', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal(),
            'count' => $this->cart->count(),
        ])->header('Cache-Control', 'no-store');
    }

    public function add(Request $request): JsonResponse|Response
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $result = $this->cart->add((int) $validated['product_id'], (int) ($validated['quantity'] ?? 1));

        if (! $result['ok']) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json($result, 422);
            }

            return back()->with('error', $result['error'])->with('available', $result['available'] ?? null);
        }

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with('success', 'added');
    }

    public function update(Request $request, int $id): JsonResponse|Response
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $result = $this->cart->update($id, (int) $validated['quantity']);

        if (! $result['ok']) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json($result, 422);
            }

            return back()->with('error', $result['error']);
        }

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with('success', 'updated');
    }

    public function remove(Request $request, int $id): JsonResponse|Response
    {
        $result = $this->cart->remove($id);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with('success', 'removed');
    }
}