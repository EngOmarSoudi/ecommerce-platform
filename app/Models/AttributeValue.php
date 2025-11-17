<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'hex_value',
        'sort_order',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function skus()
    {
        return $this->belongsToMany(Sku::class);
    }
}