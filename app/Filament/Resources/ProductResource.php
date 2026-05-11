<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use App\Services\UploadService;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

final class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $modelLabel = 'produto';

    protected static ?string $pluralModelLabel = 'produtos';

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informações')->schema([
                TextInput::make('name')->label('Título')->required()->maxLength(255),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('sku')->label('SKU')->required()->unique(ignoreRecord: true)->maxLength(255),
                Select::make('category_id')->label('Categoria')->relationship('category', 'name')->searchable()->preload(),
                Textarea::make('short_description')->label('Descrição curta')->rows(3)->columnSpanFull(),
                RichEditor::make('description')->label('Descrição completa'),
            ])->columnSpanFull(),
            Section::make('Preço e estoque')->schema([
                TextInput::make('price_cents')->label('Preço em centavos')->numeric()->required()->minValue(0),
                TextInput::make('compare_at_price_cents')->label('Preço anterior em centavos')->numeric()->minValue(0),
                TextInput::make('stock_quantity')->label('Estoque')->numeric()->required()->minValue(0)->default(0),
                TextInput::make('low_stock_threshold')->label('Alerta de estoque')->numeric()->required()->minValue(0)->default(5),
                TextInput::make('weight_kg')->label('Peso kg')->numeric()->minValue(0),
                Toggle::make('is_active')->label('Ativo')->default(true),
                Toggle::make('is_featured')->label('Destaque')->default(false),
            ])->columnSpanFull(),
            Section::make('Imagens')->schema([
                Repeater::make('productFiles')
                    ->label('Galeria')
                    ->relationship()
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Imagem')
                            ->disk(self::uploadService()->disk())
                            ->visibility('public')
                            ->image()
                            ->directory(fn (Get $get): string => self::uploadService()->productDirectoryForCategoryId(
                                $get->integer('../../category_id', isNullable: true),
                            ))
                            ->required(),
                        TextInput::make('alt_text')->label('Texto alternativo')->maxLength(255),
                        Toggle::make('is_primary')->label('Principal'),
                        Toggle::make('is_active')->label('Ativa')->default(true),
                        TextInput::make('sort_order')->label('Ordem')->numeric()->default(0),
                    ])
                    ->columns(1)
                    ->reorderable()
                    ->addActionLabel('Adicionar imagem')
                    ->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_image')
                    ->label('Imagem')
                    ->disk(self::uploadService()->disk())
                    ->getStateUsing(fn (Product $record): ?string => $record->images->first()?->file?->path)
                    ->square(),
                TextColumn::make('name')->label('Produto')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->label('Categoria')->sortable(),
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('price_cents')->label('Preço')->money('BRL', divideBy: 100)->sortable(),
                TextColumn::make('stock_quantity')->label('Estoque')->sortable(),
                IconColumn::make('is_active')->label('Ativo')->boolean(),
                IconColumn::make('is_featured')->label('Destaque')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')->label('Categoria')->relationship('category', 'name'),
                TernaryFilter::make('is_active')->label('Ativo'),
                TernaryFilter::make('is_featured')->label('Destaque'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    private static function uploadService(): UploadService
    {
        return resolve(UploadService::class);
    }
}
