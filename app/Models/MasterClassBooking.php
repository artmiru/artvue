<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterClassBooking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'master_class_id',
        'gift_certificate_id',
        'payment_status',
        'visit_status',
        'amount',
        'order_id',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'master_class_id' => 'integer',
        'gift_certificate_id' => 'integer',
        'amount' => 'integer',
        'order_id' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the user for the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the master class for the booking.
     */
    public function masterClass()
    {
        return $this->belongsTo(MasterClass::class);
    }

    /**
     * Get the gift certificate for the booking.
     */
    public function giftCertificate()
    {
        return $this->belongsTo(GiftCertificate::class);
    }
}