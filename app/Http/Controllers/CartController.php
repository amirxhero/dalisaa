<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index()
    {
        $cart = $this->cartService->currentCart();

        return view('cart.index', ['cart' => $cart]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (!$product->in_stock) {
            return back()->with('error', 'این محصول در حال حاضر ناموجود است.');
        }

        $variant = null;

        if (!empty($data['product_variant_id'])) {
            $variant = ProductVariant::where('product_id', $product->id)->find($data['product_variant_id']);
        } elseif ($product->variants()->exists()) {
            $variant = $product->variants()->where('is_default', true)->first() ?? $product->variants()->first();
        }

        $this->cartService->add($product, $variant, (int) ($data['quantity'] ?? 1));

        return redirect()->route('cart.index')->with('cart_success', $product->title);
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $this->cartService->updateQuantity($item, (int) $data['quantity']);

        return back();
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);

        $this->cartService->remove($item);

        return back()->with('success', 'محصول از سبد خرید حذف شد.');
    }

    private function authorizeItem(CartItem $item): void
    {
        $cart = $this->cartService->currentCart();

        abort_unless($item->cart_id === $cart->id, 403);
    }
}
