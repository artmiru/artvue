<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCertificate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'name',
        'amount',
        'visits_total',
        'visits_used',
        'expiry_date',
        'purchaser_name',
        'purchaser_phone',
        'purchaser_email',
        'payment_status',
        'status',
        'notes',
        'is_sent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'amount' => 'integer',
        'visits_total' => 'integer',
        'visits_used' => 'integer',
        'expiry_date' => 'datetime',
        'is_sent' => 'boolean',
    ];

    /**
     * Get the user for the gift certificate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}