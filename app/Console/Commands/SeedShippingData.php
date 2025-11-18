<?php

namespace App\Console\Commands;

use App\Models\Carrier;
use App\Models\ShippingZone;
use App\Models\ShippingRate;
use Illuminate\Console\Command;

class SeedShippingData extends Command
{
    protected $signature = 'shipping:seed';
    protected $description = 'Seed shipping zones, carriers, and rates for testing';

    public function handle()
    {
        $this->info('Seeding shipping data...');

        // Create carriers
        $aramex = Carrier::firstOrCreate(['code' => 'aramex'], [
            'name' => 'Aramex',
            'api_endpoint' => 'https://api.aramex.com/v1',
            'tracking_url_template' => 'https://www.aramex.com/track?awb={awb}',
            'is_active' => true,
        ]);

        $smsa = Carrier::firstOrCreate(['code' => 'smsa'], [
            'name' => 'SMSA Express',
            'api_endpoint' => 'https://api.smsaexpress.com',
            'tracking_url_template' => 'https://www.smsaexpress.com/track?awb={awb}',
            'is_active' => true,
        ]);

        $this->info('✓ Carriers created: Aramex, SMSA');

        // Create shipping zones
        $domesticZone = ShippingZone::firstOrCreate(['name' => 'Domestic (USA)'], [
            'countries' => ['USA'],
            'is_active' => true,
        ]);

        $gccZone = ShippingZone::firstOrCreate(['name' => 'GCC Countries'], [
            'countries' => ['SAU', 'UAE', 'KWT', 'BHR', 'QAT', 'OMN'],
            'is_active' => true,
        ]);

        $internationalZone = ShippingZone::firstOrCreate(['name' => 'International'], [
            'countries' => ['*'], // All other countries
            'is_active' => true,
        ]);

        $this->info('✓ Shipping zones created: Domestic, GCC, International');

        // Create shipping rates
        ShippingRate::firstOrCreate([
            'shipping_zone_id' => $domesticZone->id,
            'carrier_id' => $aramex->id,
            'name' => 'Standard Domestic',
        ], [
            'calculation_method' => 'weight',
            'base_rate' => 5.00,
            'rate_per_kg' => 2.50,
            'estimated_days_min' => 3,
            'estimated_days_max' => 5,
            'is_active' => true,
        ]);

        ShippingRate::firstOrCreate([
            'shipping_zone_id' => $domesticZone->id,
            'carrier_id' => $aramex->id,
            'name' => 'Express Domestic',
        ], [
            'calculation_method' => 'weight',
            'base_rate' => 10.00,
            'rate_per_kg' => 3.50,
            'estimated_days_min' => 1,
            'estimated_days_max' => 2,
            'is_active' => true,
        ]);

        ShippingRate::firstOrCreate([
            'shipping_zone_id' => $gccZone->id,
            'carrier_id' => $smsa->id,
            'name' => 'GCC Standard',
        ], [
            'calculation_method' => 'weight',
            'base_rate' => 15.00,
            'rate_per_kg' => 5.00,
            'estimated_days_min' => 5,
            'estimated_days_max' => 7,
            'is_active' => true,
        ]);

        ShippingRate::firstOrCreate([
            'shipping_zone_id' => $internationalZone->id,
            'carrier_id' => $aramex->id,
            'name' => 'International Standard',
        ], [
            'calculation_method' => 'weight',
            'base_rate' => 25.00,
            'rate_per_kg' => 8.00,
            'estimated_days_min' => 7,
            'estimated_days_max' => 14,
            'is_active' => true,
        ]);

        $this->info('✓ Shipping rates created: 4 rates across different zones');
        $this->info('');
        $this->info('Shipping data seeded successfully!');
        $this->info('');
        $this->info('Available carriers:');
        $this->table(['ID', 'Code', 'Name', 'Active'], Carrier::all(['id', 'code', 'name', 'is_active'])->toArray());
        $this->info('');
        $this->info('Available zones:');
        $this->table(['ID', 'Name', 'Countries', 'Active'], ShippingZone::all(['id', 'name', 'countries', 'is_active'])->toArray());

        return 0;
    }
}
