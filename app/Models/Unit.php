<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'description',
        'conversion_factor',
        'is_base_unit',
        'is_active',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:4',
        'is_base_unit' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function skus()
    {
        return $this->belongsToMany(Sku::class)->withPivot('quantity_per_unit', 'price_modifier', 'is_default');
    }
}