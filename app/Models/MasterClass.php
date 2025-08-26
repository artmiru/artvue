<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterClass extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'price',
        'booked_places',
        'image_path',
        'page_title',
        'meta_description',
        'max_participants',
        'is_active',
        'tags',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'integer',
        'booked_places' => 'integer',
        'max_participants' => 'integer',
        'is_active' => 'boolean',
        'tags' => 'array',
    ];
}