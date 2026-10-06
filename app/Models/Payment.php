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
     * Mark all pending payments of a user as expired when their expiry time has passed.
     */
    public static function expirePendingForUser($userId): void
    {
        static::whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->where('expires_at', '<', now())
                  ->orWhereHas('order', function ($q2) {
                      $q2->where('expires_at', '<', now());
                  });
            })
            ->update(['status' => 'expired']);
    }

    /**
     * Mark all overdue pending payments (including guest orders) as expired.
     */
    public static function expireOverdue(): int
    {
        return static::where('status', 'pending')
            ->where(function ($q) {
                $q->where('expires_at', '<', now())
                  ->orWhereHas('order', function ($q2) {
                      $q2->where('expires_at', '<', now());
                  });
            })
            ->update(['status' => 'expired']);
    }

    /**
     * Get the order that owns the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the consistent Indonesian label for this payment's status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'completed' => 'Pembayaran Berhasil',
            'failed' => 'Pembayaran Gagal',
            'cancelled' => 'Pembayaran Dibatalkan',
            'expired' => 'Pembayaran Kedaluwarsa',
            default => ucfirst((string) $this->status),
        };
    }
}
