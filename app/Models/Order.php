<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference',
        'total_price',
        'quantity',
        'email',
        'guest_name',
        'guest_phone',
        'order_date',
        'expires_at',
        'ticket_id',
        'user_id',
        'payment_gateway_reference'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'datetime',
        'expires_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the ticket that owns the order.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the payment associated with the order.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Check if the order is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    /**
     * Check if the order is paid.
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->payment && $this->payment->status === 'completed';
    }

    /**
     * Check if the order is pending payment.
     *
     * @return bool
     */
    public function isPending()
    {
        return !$this->payment || $this->payment->status === 'pending';
    }

    /**
     * Get the formatted status of the order.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        if ($this->isPaid()) {
            return 'paid';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if ($this->payment && $this->payment->status === 'failed') {
            return 'failed';
        }

        return 'pending';
    }

    /**
     * Get the consistent Indonesian label for this order's status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Pembayaran Berhasil',
            'expired' => 'Pembayaran Kedaluwarsa',
            'failed' => 'Pembayaran Gagal',
            default => $this->payment ? 'Menunggu Pembayaran' : 'Belum Dibayar',
        };
    }

    /**
     * Get formatted remaining time until expiration.
     *
     * @return string|null
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        $now = now();

        if ($now->isAfter($this->expires_at)) {
            return 'Expired';
        }

        $diff = $now->diff($this->expires_at);

        if ($diff->days > 0) {
            return $diff->format('%d days, %h hours');
        }

        if ($diff->h > 0) {
            return $diff->format('%h hours, %i minutes');
        }

        return $diff->format('%i minutes, %s seconds');
    }

    /**
     * Set the default expiration time when creating a new order.
     */
    protected static function booted()
    {
        static::creating(function ($order) {
            // Set default expiration time to 1 hour from now if not already set
            if (!$order->expires_at) {
                $order->expires_at = now()->addHour();
            }
        });
    }
}
