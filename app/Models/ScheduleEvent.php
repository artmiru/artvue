<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleEvent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_datetime',
        'template_id',
        'teacher_id',
        'master_class_id',
        'booked_count',
        'max_participants',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_datetime' => 'datetime',
        'template_id' => 'integer',
        'teacher_id' => 'integer',
        'master_class_id' => 'integer',
        'booked_count' => 'integer',
        'max_participants' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the template for the schedule event.
     */
    public function template()
    {
        return $this->belongsTo(ScheduleTemplate::class);
    }

    /**
     * Get the teacher for the schedule event.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the master class for the schedule event.
     */
    public function masterClass()
    {
        return $this->belongsTo(MasterClass::class);
    }
}