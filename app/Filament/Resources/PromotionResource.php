<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Promotions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promotion Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('promotion_type')
                            ->options([
                                'product_discount' => 'Product Discount',
                                'flash_sale' => 'Flash Sale',
                                'bundle' => 'Bundle Deal',
                                'buy_x_get_y' => 'Buy X Get Y',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Discount Configuration')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed_amount' => 'Fixed Amount',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('discount_value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn ($get) => $get('discount_type') === 'percentage' ? '%' : '$'),
                        Forms\Components\TextInput::make('max_discount_amount')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->helperText('Max discount for percentage promotions'),
                    ])->columns(3),

                Forms\Components\Section::make('Applicability')
                    ->schema([
                        Forms\Components\Select::make('product_ids')
                            ->label('Products')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty to apply to all products'),
                        Forms\Components\Select::make('category_ids')
                            ->label('Categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Apply to all products in selected categories'),
                    ])->columns(2),

                Forms\Components\Section::make('Conditions')
                    ->schema([
                        Forms\Components\TextInput::make('minimum_quantity')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Minimum quantity required'),
                        Forms\Components\TextInput::make('minimum_purchase')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->helperText('Minimum purchase amount'),
                    ])->columns(2),

                Forms\Components\Section::make('Schedule & Status')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->required()
                            ->after('start_date'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(false)
                            ->helperText('For flash sales, this will be auto-managed by scheduler'),
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
                Tables\Columns\BadgeColumn::make('promotion_type')
                    ->colors([
                        'danger' => 'flash_sale',
                        'success' => 'product_discount',
                        'warning' => 'bundle',
                        'primary' => 'buy_x_get_y',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Discount')
                    ->formatStateUsing(fn ($record) => $record->discount_type === 'percentage' 
                        ? "{$record->discount_value}%" 
                        : "\${$record->discount_value}"),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('promotion_type')
                    ->options([
                        'product_discount' => 'Product Discount',
                        'flash_sale' => 'Flash Sale',
                        'bundle' => 'Bundle Deal',
                        'buy_x_get_y' => 'Buy X Get Y',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
