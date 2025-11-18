<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => config('app.name'),
            'site_email' => config('mail.from.address'),
            'tax_rate' => 10,
            'currency' => 'USD',
            'free_shipping_threshold' => 100,
            'low_stock_threshold' => 10,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Settings')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('site_email')
                            ->email()
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'US Dollar',
                                'EUR' => 'Euro',
                                'GBP' => 'British Pound',
                                'SAR' => 'Saudi Riyal',
                                'AED' => 'UAE Dirham',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Tax & Shipping')
                    ->schema([
                        Forms\Components\TextInput::make('tax_rate')
                            ->numeric()
                            ->suffix('%')
                            ->required()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('free_shipping_threshold')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->helperText('Minimum order amount for free shipping'),
                        Forms\Components\TextInput::make('low_stock_threshold')
                            ->numeric()
                            ->required()
                            ->helperText('Alert when stock falls below this number'),
                    ])->columns(3),

                Forms\Components\Section::make('Payment Gateways')
                    ->schema([
                        Forms\Components\Toggle::make('enable_stripe')
                            ->label('Enable Stripe'),
                        Forms\Components\Toggle::make('enable_cash_on_delivery')
                            ->label('Enable Cash on Delivery')
                            ->default(true),
                        Forms\Components\Toggle::make('enable_apple_pay')
                            ->label('Enable Apple Pay'),
                    ])->columns(3),

                Forms\Components\Section::make('Email Notifications')
                    ->schema([
                        Forms\Components\Toggle::make('notify_order_created')
                            ->label('Order Created')
                            ->default(true),
                        Forms\Components\Toggle::make('notify_order_shipped')
                            ->label('Order Shipped')
                            ->default(true),
                        Forms\Components\Toggle::make('notify_low_stock')
                            ->label('Low Stock Alert')
                            ->default(true),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // In a real application, save to database or .env
        // For now, just show success notification
        
        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
