<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id',
        'carrier_id',
        'name',
        'calculation_method',
        'base_rate',
        'rate_per_kg',
        'rate_per_unit',
        'min_weight',
        'max_weight',
        'min_dimensions',
        'max_dimensions',
        'estimated_days_min',
        'estimated_days_max',
        'is_active',
    ];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'rate_per_kg' => 'decimal:2',
        'rate_per_unit' => 'decimal:2',
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'min_dimensions' => 'array',
        'max_dimensions' => 'array',
        'estimated_days_min' => 'integer',
        'estimated_days_max' => 'integer',
        'is_active' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Calculate shipping cost
     */
    public function calculateCost(float $weight, array $dimensions = [], int $quantity = 1): float
    {
        $cost = $this->base_rate;

        switch ($this->calculation_method) {
            case 'weight':
                $cost += $weight * $this->rate_per_kg;
                break;
            case 'quantity':
                $cost += $quantity * $this->rate_per_unit;
                break;
            case 'flat':
            default:
                // Base rate only
                break;
        }

        return round($cost, 2);
    }
}
