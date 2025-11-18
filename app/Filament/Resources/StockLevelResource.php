<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockLevelResource\Pages;
use App\Models\StockLevel;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockLevelResource extends Resource
{
    protected static ?string $model = StockLevel::class;
    protected static ?string $navigationLabel = 'Stock Levels';
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cube';
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Stock Information')
                    ->schema([
                        Forms\Components\Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->required(),
                        Forms\Components\Select::make('sku_id')
                            ->relationship('sku', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(0),
                        Forms\Components\TextInput::make('available_quantity')
                            ->numeric()
                            ->required()
                            ->default(0),
                        Forms\Components\TextInput::make('reserved_quantity')
                            ->numeric()
                            ->required()
                            ->default(0),
                        Forms\Components\TextInput::make('reorder_point')
                            ->numeric()
                            ->default(10)
                            ->helperText('Alert when stock falls below this level'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku.product.name')
                    ->label('Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->color(fn ($record) => $record->quantity <= $record->reorder_point ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('available_quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->getStateUsing(fn ($record) => $record->quantity <= 0 ? 'Out of Stock' : 
                        ($record->quantity <= $record->reorder_point ? 'Low Stock' : 'In Stock'))
                    ->colors([
                        'danger' => 'Out of Stock',
                        'warning' => 'Low Stock',
                        'success' => 'In Stock',
                    ]),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name'),
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn ($query) => $query->whereColumn('quantity', '<=', 'reorder_point')),
                Tables\Filters\Filter::make('out_of_stock')
                    ->query(fn ($query) => $query->where('quantity', '<=', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('adjust_stock')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->form([
                        Forms\Components\TextInput::make('adjustment')
                            ->numeric()
                            ->required()
                            ->helperText('Enter positive to add, negative to subtract'),
                        Forms\Components\Textarea::make('reason')
                            ->required(),
                    ])
                    ->action(function (StockLevel $record, array $data) {
                        $record->quantity += $data['adjustment'];
                        $record->available_quantity += $data['adjustment'];
                        $record->save();
                        
                        // Log stock movement
                        \App\Models\StockMovement::create([
                            'sku_id' => $record->sku_id,
                            'warehouse_id' => $record->warehouse_id,
                            'movement_type' => 'adjustment',
                            'quantity' => $data['adjustment'],
                            'notes' => $data['reason'],
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('quantity', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockLevels::route('/'),
            'create' => Pages\CreateStockLevel::route('/create'),
            'edit' => Pages\EditStockLevel::route('/{record}/edit'),
        ];
    }
}
