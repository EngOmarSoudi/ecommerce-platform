<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBehavior extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'event_type',
        'quantity',
        'price',
        'metadata',
        'event_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'event_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Record a user behavior event
     */
    public static function recordEvent(string $eventType, int $productId, array $data = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'product_id' => $productId,
            'event_type' => $eventType,
            'quantity' => $data['quantity'] ?? 1,
            'price' => $data['price'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'event_at' => now(),
        ]);
    }
}
