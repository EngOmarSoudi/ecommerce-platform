<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'api_endpoint',
        'api_key',
        'api_secret',
        'tracking_url_template',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Get tracking URL for AWB number
     */
    public function getTrackingUrl(string $awbNumber): string
    {
        return str_replace('{awb}', $awbNumber, $this->tracking_url_template);
    }
}
