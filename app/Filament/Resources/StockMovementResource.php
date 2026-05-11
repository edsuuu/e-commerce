<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages\CreateStockMovement;
use App\Filament\Resources\StockMovementResource\Pages\EditStockMovement;
use App\Filament\Resources\StockMovementResource\Pages\ListStockMovements;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

final class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Movimentos de estoque';

    protected static ?string $modelLabel = 'movimento de estoque';

    protected static ?string $pluralModelLabel = 'movimentos de estoque';

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Movimento')->schema([
                Select::make('product_id')->label('Produto')->relationship('product', 'name')->searchable()->preload()->required(),
                Select::make('type')->label('Tipo')->options([
                    StockMovement::TYPE_IN => 'Entrada',
                    StockMovement::TYPE_OUT => 'Saída',
                    StockMovement::TYPE_ADJUSTMENT => 'Ajuste',
                ])->required(),
                TextInput::make('quantity')->label('Quantidade')->numeric()->required(),
                TextInput::make('stock_after')->label('Estoque após movimento')->numeric()->disabled()->dehydrated(false),
                TextInput::make('reason')->label('Motivo')->maxLength(255),
                Textarea::make('notes')->label('Observações')->rows(4),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Produto')->searchable()->sortable(),
                TextColumn::make('type')->label('Tipo')->badge(),
                TextColumn::make('quantity')->label('Qtd.')->sortable(),
                TextColumn::make('stock_after')->label('Saldo')->sortable(),
                TextColumn::make('reason')->label('Motivo')->searchable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->label('Tipo')->options([
                    StockMovement::TYPE_IN => 'Entrada',
                    StockMovement::TYPE_OUT => 'Saída',
                    StockMovement::TYPE_ADJUSTMENT => 'Ajuste',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
            'edit' => EditStockMovement::route('/{record}/edit'),
        ];
    }
}
