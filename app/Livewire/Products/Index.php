<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Produtos')]
final class Index extends Component
{
    use WithPagination;

    #[Url(as: 'busca')]
    public string $search = '';

    public ?int $category = null;

    public string $sort = 'featured';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::query()->available()->findOrFail($productId);

        $cart = $this->cart();
        $currentItem = $cart[$product->id] ?? ['quantity' => 0];
        $quantity = min($currentItem['quantity'] + 1, $product->stock_quantity);

        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => $quantity,
        ];

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }

    public function buyNow(int $productId): void
    {
        $this->addToCart($productId);
        $this->redirectRoute('cart.show', navigate: true);
    }

    public function render(): View
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->available()
            ->when($this->search !== '', fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('name', 'like', sprintf('%%%s%%', $this->search))
                    ->orWhere('sku', 'like', sprintf('%%%s%%', $this->search))
            ))
            ->when($this->category, fn ($query) => $query->where('category_id', $this->category))
            ->when($this->sort === 'price_asc', fn ($query) => $query->orderBy('price_cents'))
            ->when($this->sort === 'price_desc', fn ($query) => $query->orderByDesc('price_cents'))
            ->when($this->sort === 'newest', fn ($query) => $query->latest())
            ->when($this->sort === 'featured', fn ($query) => $query->orderByDesc('is_featured')->latest())
            ->paginate(12);

        return view('livewire.products.index', [
            'categories' => Category::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
            'products' => $products,
        ]);
    }

    /**
     * @return array<int, array{product_id: int, quantity: int}>
     */
    private function cart(): array
    {
        $cart = session()->get('cart', []);

        if (! is_array($cart)) {
            return [];
        }

        $normalized = [];

        foreach ($cart as $productId => $item) {
            if (! is_numeric($productId)) {
                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            if (! isset($item['quantity'])) {
                continue;
            }

            if (! is_numeric($item['quantity'])) {
                continue;
            }

            $rawProductId = $item['product_id'] ?? $productId;

            if (! is_numeric($rawProductId)) {
                continue;
            }

            $normalized[(int) $productId] = [
                'product_id' => (int) $rawProductId,
                'quantity' => (int) $item['quantity'],
            ];
        }

        return $normalized;
    }
}
