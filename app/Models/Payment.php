<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'method',
        'status',
        'guest_email',
        'payment_date',
        'order_id',
        'transaction_id',
        'snap_token',
        'payment_instruction',
        'expires_at',
        'payment_type',
        'payment_data'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'expires_at' => 'datetime',
        'payment_data' => 'array',
    ];


    public function isExpired()
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    /**
     * Get the order that owns the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
