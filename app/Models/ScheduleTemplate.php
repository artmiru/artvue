<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'day_of_week',
        'start_time',
        'teacher_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'teacher_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the teacher for the schedule template.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}