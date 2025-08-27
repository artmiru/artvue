<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'alt',
        'description',
        'price',
        'slug',
        'meta_title',
        'meta_description',
        'category',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'integer',
    ];

    /**
     * Get the price in rubles.
     *
     * @param  int  $value
     * @return float
     */
    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    /**
     * Set the price in rubles.
     *
     * @param  float  $value
     * @return void
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}