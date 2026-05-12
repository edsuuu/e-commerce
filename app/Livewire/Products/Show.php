<?php

declare(strict_types=1);

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductFile;
use App\Services\Cart\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Show extends Component
{
    public Product $product;

    /** @var array<int, array{url: string, alt: string}> */
    public array $gallery = [];

    public int $selectedImageIndex = 0;

    public function mount(Product $product): void
    {
        abort_unless($this->isAvailableForStore($product), 404);

        resolve(CartService::class)->migrateSessionCartToDatabase();

        $this->product = $product->load(['category', 'images.file']);
        $this->gallery = $this->buildGallery($this->product);
    }

    public function selectImage(int $index): void
    {
        if (! array_key_exists($index, $this->gallery)) {
            return;
        }

        $this->selectedImageIndex = $index;
    }

    public function addToCart(): void
    {
        resolve(CartService::class)->add($this->product);
        $this->dispatch('cart-updated');
    }

    public function buyNow(): void
    {
        $this->addToCart();
        $this->redirectRoute('cart.show', navigate: true);
    }

    /**
     * @return array<int, array{url: string, alt: string}>
     */
    private function buildGallery(Product $product): array
    {
        $images = $product->images
            ->map(fn (ProductFile $image): ?array => filled($image->file?->url) ? [
                'url' => $image->file->url,
                'alt' => $image->alt_text ?? $product->name,
            ] : null)
            ->filter()
            ->values()
            ->all();

        if ($images !== []) {
            return $images;
        }

        return [[
            'url' => $product->primary_image_url,
            'alt' => $product->name,
        ]];
    }

    private function discountPercentage(): ?int
    {
        if (
            $this->product->compare_at_price_cents === null
            || $this->product->compare_at_price_cents <= $this->product->price_cents
        ) {
            return null;
        }

        return (int) round((1 - ($this->product->price_cents / $this->product->compare_at_price_cents)) * 100);
    }

    /**
     * @return Collection<int, Product>
     */
    private function relatedProducts(): Collection
    {
        return Product::query()
            ->with(['category', 'images.file'])
            ->available()
            ->whereKeyNot($this->product->id)
            ->when(
                $this->product->category_id !== null,
                fn ($query) => $query->where('category_id', $this->product->category_id),
            )
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(4)
            ->get();
    }

    /**
     * @return array<int, int>
     */
    private function ratingDistribution(): array
    {
        $counts = $this->product
            ->reviews()
            ->where('is_approved', true)
            ->selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $distribution = [];

        foreach ([5, 4, 3, 2, 1] as $rating) {
            $count = $counts->get($rating, 0);
            $distribution[$rating] = is_numeric($count) ? (int) $count : 0;
        }

        return $distribution;
    }

    private function isAvailableForStore(Product $product): bool
    {
        $publishedAt = is_string($product->published_at)
            ? Carbon::parse($product->published_at)
            : $product->published_at;

        return $product->is_active
            && $product->stock_quantity > 0
            && ($publishedAt === null || $publishedAt->lte(now()));
    }

    public function render(): View
    {
        $reviewQuery = $this->product->reviews()->where('is_approved', true);
        $reviewCount = (int) $reviewQuery->count();
        $averageRating = $reviewCount > 0 ? round((float) $reviewQuery->avg('rating'), 1) : 0.0;

        return view('livewire.products.show', [
            'averageRating' => $averageRating,
            'discountPercentage' => $this->discountPercentage(),
            'installmentValueCents' => (int) ceil($this->product->price_cents / 10),
            'ratingDistribution' => $this->ratingDistribution(),
            'relatedProducts' => $this->relatedProducts(),
            'reviewCount' => $reviewCount,
            'reviews' => $this->product->reviews()->where('is_approved', true)->latest()->limit(8)->get(),
            'selectedImage' => $this->gallery[$this->selectedImageIndex]['url'] ?? $this->product->primary_image_url,
        ]);
    }
}
