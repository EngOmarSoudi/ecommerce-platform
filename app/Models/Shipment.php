<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'carrier_id',
        'tracking_number',
        'awb_number',
        'carrier',
        'shipping_method',
        'weight',
        'tracking_url',
        'label_url',
        'shipped_at',
        'estimated_delivery_at',
        'actual_delivery_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'shipped_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'actual_delivery_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}