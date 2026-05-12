<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Services\Cart\CartService;
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

    #[Url]
    public ?int $category = null;

    public string $sort = 'featured';

    public function mount(): void
    {
        resolve(CartService::class)->migrateSessionCartToDatabase();
    }

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
        resolve(CartService::class)->add($productId);
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
}
