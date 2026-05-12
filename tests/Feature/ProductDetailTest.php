<?php

declare(strict_types=1);

use App\Models\File as MediaFile;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductReview;
use Database\Seeders\Seeder003Catalog;

test('product cards link to product detail page', function (): void {
    $product = Product::query()->create([
        'name' => 'Placa de Video RTX 5070',
        'slug' => 'placa-de-video-rtx-5070',
        'sku' => 'GPU-5070',
        'short_description' => 'Placa de video com pronta entrega.',
        'description' => '<p>Descricao completa do produto.</p>',
        'price_cents' => 429990,
        'compare_at_price_cents' => 479990,
        'stock_quantity' => 4,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('products.show', $product->slug), false);
});

test('product detail page renders product information', function (): void {
    $product = Product::query()->create([
        'name' => 'Placa de Video RTX 5070',
        'slug' => 'placa-de-video-rtx-5070',
        'sku' => 'GPU-5070',
        'short_description' => 'Placa de video com pronta entrega.',
        'description' => '<p>Descricao completa do produto.</p>',
        'price_cents' => 429990,
        'compare_at_price_cents' => 479990,
        'stock_quantity' => 4,
        'is_active' => true,
        'published_at' => now(),
    ]);

    ProductReview::query()->create([
        'product_id' => $product->id,
        'customer_name' => 'Cliente Teste',
        'rating' => 5,
        'title' => 'Excelente compra',
        'comment' => 'Funcionou perfeitamente no meu setup.',
        'is_approved' => true,
    ]);

    $this->get(route('products.show', $product->slug))
        ->assertOk()
        ->assertSee('Placa de Video RTX 5070')
        ->assertSee('GPU-5070')
        ->assertSee('Descricao completa do produto')
        ->assertSee('Opinião de quem comprou')
        ->assertSee('Excelente compra')
        ->assertSee('Comprar agora');
});

test('unavailable product detail page returns not found', function (): void {
    $product = Product::query()->create([
        'name' => 'Produto sem estoque',
        'slug' => 'produto-sem-estoque',
        'sku' => 'NO-STOCK',
        'price_cents' => 1000,
        'stock_quantity' => 0,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $this->get(route('products.show', $product->slug))->assertNotFound();
});

test('catalog seeder creates a complete storefront catalog', function (): void {
    $this->seed(Seeder003Catalog::class);

    expect(Product::query()->count())->toBeGreaterThanOrEqual(30)
        ->and(ProductFile::query()->where('is_primary', true)->count())->toBeGreaterThanOrEqual(30)
        ->and(ProductReview::query()->count())->toBeGreaterThanOrEqual(120)
        ->and(MediaFile::query()->where('path', 'like', 'https://images%.kabum.com.br/%')->count())->toBeGreaterThanOrEqual(15)
        ->and(MediaFile::query()->where('path', 'like', '/images/products/%')->count())->toBe(0);
});
