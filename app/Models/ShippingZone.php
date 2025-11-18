<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'countries',
        'states',
        'postal_codes',
        'is_active',
    ];

    protected $casts = [
        'countries' => 'array',
        'states' => 'array',
        'postal_codes' => 'array',
        'is_active' => 'boolean',
    ];

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    /**
     * Check if address is in this zone
     */
    public function containsAddress(Address $address): bool
    {
        // Check country
        if (!empty($this->countries) && !in_array($address->country, $this->countries)) {
            return false;
        }

        // Check state
        if (!empty($this->states) && !in_array($address->state, $this->states)) {
            return false;
        }

        // Check postal code (simplified - use regex patterns in production)
        if (!empty($this->postal_codes) && !in_array($address->postal_code, $this->postal_codes)) {
            return false;
        }

        return true;
    }
}
