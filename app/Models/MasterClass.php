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
     * Get the formatted price in rubles.
     *
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        // Если цена целая (без копеек), не показываем .00
        if (floor($this->price) == $this->price) {
            return number_format($this->price, 0, '.', ' ') . ' ₽';
        } else {
            return number_format($this->price, 2, '.', ' ') . ' ₽';
        }
    }

    /**
     * Get the formatted next event date.
     *
     * @return string|null
     */
    public function getFormattedNextEventDateAttribute()
    {
        if (!isset($this->next_event_date)) {
            return null;
        }

        $date = new \DateTime($this->next_event_date);
        
        // День месяца
        $day = $date->format('j');
        
        // Сокращенные названия месяцев на русском
        $months = [
            'янв.', 'февр.', 'мар.', 'апр.', 'мая', 'июн.',
            'июл.', 'авг.', 'сент.', 'окт.', 'нояб.', 'дек.'
        ];
        $month = $months[$date->format('n') - 1];
        
        // Время в формате ЧЧ:ММ
        $time = $date->format('H:i');
        
        // Дни недели на русском
        $weekdays = [
            'воскресенье', 'понедельник', 'вторник', 'среда',
            'четверг', 'пятница', 'суббота'
        ];
        $weekday = $weekdays[$date->format('w')];
        
        return "{$day} {$month} в {$time} ({$weekday})";
    }
}