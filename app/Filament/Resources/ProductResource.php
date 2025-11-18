<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(4),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name'),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('compare_at_price')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('price_visibility')
                            ->options([
                                'public' => 'Public',
                                'b2b' => 'B2B Only',
                                'authenticated' => 'Authenticated Users',
                            ])
                            ->default('public'),
                    ])->columns(3),

                Forms\Components\Section::make('Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'draft' => 'Draft',
                                'archived' => 'Archived',
                            ])
                            ->default('active'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'draft',
                        'danger' => 'archived',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
