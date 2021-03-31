<?php

namespace Vladmeh\PaymentManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Nonstandard\Uuid;
use Vladmeh\PaymentManager\Casts\PaymentJson;
use Vladmeh\PaymentManager\Contracts\PayableOrder;
use Vladmeh\PaymentManager\Order\PayableOrderTrait;
use Vladmeh\PaymentManager\Pscb\PaymentStatus;

class PaymentOrder extends Model implements PayableOrder
{
    use PayableOrderTrait;

    protected $table = 'orders';

    public $incrementing = false;

    protected $guarded = [];
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    protected $attributes = [
        'details' => ''
    ];

    protected $casts = [
        'payment' => PaymentJson::class,
    ];

    /**
     * @param $value
     */
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = $value;
        $this->attributes['uuid'] = Uuid::uuid6();
    }

    /**
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(PaymentOrderItem::class, 'order_id', 'uuid');
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(PaymentCustomer::class);
    }

    /**
     * @param string $state
     * @return void
     */
    public function setStatus(string $state)
    {
        self::update(['state' => $state]);
    }

    /**
     * @param mixed $payment
     * @return void
     */
    public function setPayment($payment)
    {
        if (is_array($payment) && array_key_exists('payment', $payment)) {
            $paymentStatus = $payment['payment']['state']
                ? PaymentStatus::status($payment['payment']['state'])
                : PaymentStatus::UNDEF;

            $this->update([
                'payment' => $payment['payment'],
                'state' => $paymentStatus
            ]);
        }
    }
}
