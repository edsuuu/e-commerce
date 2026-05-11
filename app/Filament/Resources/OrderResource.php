<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

final class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $modelLabel = 'pedido';

    protected static ?string $pluralModelLabel = 'pedidos';

    protected static string|UnitEnum|null $navigationGroup = 'Vendas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Status')->schema([
                Select::make('status')->label('Status')->options(self::statusOptions())->required(),
            ]),
            Section::make('Cliente')->schema([
                TextInput::make('customer_name')->label('Nome')->required(),
                TextInput::make('customer_email')->label('E-mail')->email()->required(),
                TextInput::make('customer_phone')->label('Telefone'),
            ])->columns(3),
            Section::make('Entrega')->schema([
                TextInput::make('shipping_zipcode')->label('CEP')->required(),
                TextInput::make('shipping_address')->label('Endereço')->required(),
                TextInput::make('shipping_number')->label('Número'),
                TextInput::make('shipping_complement')->label('Complemento'),
                TextInput::make('shipping_district')->label('Bairro'),
                TextInput::make('shipping_city')->label('Cidade')->required(),
                TextInput::make('shipping_state')->label('UF')->required()->maxLength(2),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('Pedido')->searchable()->sortable(),
                TextColumn::make('customer_name')->label('Cliente')->searchable(),
                TextColumn::make('status')->label('Status')->badge()->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state),
                TextColumn::make('total_cents')->label('Total')->money('BRL', divideBy: 100)->sortable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(self::statusOptions()),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            Order::STATUS_PENDING => 'Pendente',
            Order::STATUS_PAID => 'Pago',
            Order::STATUS_SHIPPED => 'Enviado',
            Order::STATUS_COMPLETED => 'Concluído',
            Order::STATUS_CANCELED => 'Cancelado',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
