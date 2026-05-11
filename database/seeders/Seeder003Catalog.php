<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class Seeder003Catalog extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Hardware', 'description' => 'Peças para montar ou atualizar seu computador.'],
            ['name' => 'Periféricos', 'description' => 'Teclados, mouses, headsets e acessórios.'],
            ['name' => 'Monitores', 'description' => 'Telas gamer e profissionais.'],
        ])->map(fn (array $category, int $index): Category => Category::query()->updateOrCreate(
            ['slug' => Str::slug($category['name'])],
            [
                ...$category,
                'is_active' => true,
                'sort_order' => $index + 1,
            ],
        ));

        $products = [
            ['category' => 'Hardware', 'name' => 'Placa de Vídeo RTX 5070 12GB GDDR7', 'sku' => 'GPU-RTX5070-12G', 'price_cents' => 429990, 'compare_at_price_cents' => 479990, 'stock_quantity' => 12],
            ['category' => 'Hardware', 'name' => 'Processador Ryzen 7 9700X 8-Core', 'sku' => 'CPU-R79700X', 'price_cents' => 219990, 'compare_at_price_cents' => 249990, 'stock_quantity' => 18],
            ['category' => 'Periféricos', 'name' => 'Teclado Mecânico RGB Switch Brown', 'sku' => 'KEY-RGB-BROWN', 'price_cents' => 34990, 'compare_at_price_cents' => 42990, 'stock_quantity' => 40],
            ['category' => 'Periféricos', 'name' => 'Mouse Gamer 26000 DPI Wireless', 'sku' => 'MOU-26K-WL', 'price_cents' => 28990, 'compare_at_price_cents' => 32990, 'stock_quantity' => 36],
            ['category' => 'Monitores', 'name' => 'Monitor Gamer 27 Pol QHD 180Hz IPS', 'sku' => 'MON-27QHD-180', 'price_cents' => 159990, 'compare_at_price_cents' => 189990, 'stock_quantity' => 9],
            ['category' => 'Monitores', 'name' => 'Monitor Ultrawide 34 Pol WQHD 165Hz', 'sku' => 'MON-34UW-165', 'price_cents' => 249990, 'compare_at_price_cents' => null, 'stock_quantity' => 7],
        ];

        foreach ($products as $index => $product) {
            $category = $categories->firstWhere('name', $product['category']);

            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'category_id' => $category?->id,
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']),
                    'short_description' => 'Oferta especial com envio rápido, garantia nacional e pronta entrega.',
                    'description' => '<p>Produto cadastrado para demonstração do catálogo, carrinho e fluxo de compra.</p>',
                    'price_cents' => $product['price_cents'],
                    'compare_at_price_cents' => $product['compare_at_price_cents'],
                    'stock_quantity' => $product['stock_quantity'],
                    'low_stock_threshold' => 5,
                    'is_active' => true,
                    'is_featured' => $index < 3,
                    'published_at' => now(),
                ],
            );
        }
    }
}
