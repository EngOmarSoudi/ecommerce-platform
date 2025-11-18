<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopProductsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('is_active', true)
                    ->orderByDesc('views_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('price')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable()
                    ->default(0),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ]);
    }
}
