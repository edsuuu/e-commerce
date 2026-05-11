<?php

declare(strict_types=1);

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use App\Models\Product;
use App\Models\StockMovement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

final class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $productId = $data['product_id'];
        $rawQuantity = $data['quantity'];

        abort_unless(is_numeric($productId) && is_numeric($rawQuantity), 422);

        $product = Product::query()->findOrFail((int) $productId);

        $quantity = (int) $rawQuantity;

        if ($data['type'] === StockMovement::TYPE_OUT && $quantity > 0) {
            $quantity *= -1;
        }

        if ($data['type'] === StockMovement::TYPE_IN && $quantity < 0) {
            $quantity = abs($quantity);
        }

        $stockAfter = max(0, $product->stock_quantity + $quantity);
        $product->update(['stock_quantity' => $stockAfter]);

        return [
            ...$data,
            'user_id' => Auth::id(),
            'quantity' => $quantity,
            'stock_after' => $stockAfter,
        ];
    }
}
