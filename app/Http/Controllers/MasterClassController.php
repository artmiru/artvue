<?php

namespace App\Http\Controllers;

use App\Models\MasterClass;
use Illuminate\Http\Request;

class MasterClassController extends Controller
{
    /**
     * Display a listing of the master classes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Получаем ближайшие события расписания (на ближайшие 30 дней)
        $upcomingEvents = \App\Models\ScheduleEvent::with(['masterClass'])
            ->where('start_datetime', '>=', now())
            ->where('start_datetime', '<=', now()->addDays(30))
            ->whereHas('masterClass') // Только события с привязанным мастер-классом
            ->orderBy('start_datetime', 'asc')
            ->get();
        
        // Группируем события по мастер-классам
        $eventsByMasterClass = $upcomingEvents->groupBy('master_class_id');
        
        // Извлекаем уникальные мастер-классы и добавляем информацию о ближайшей дате
        $masterClasses = $eventsByMasterClass->map(function ($events, $masterClassId) {
            $masterClass = $events->first()->masterClass;
            $nextEventDate = $events->first()->start_datetime;
            $bookedCount = $events->first()->booked_count;
            $maxParticipants = $events->first()->max_participants;
            
            // Добавляем информацию о ближайшей дате к данным мастер-класса
            $masterClass->next_event_date = $nextEventDate;
            
            // Добавляем информацию о забронированных местах из события
            $masterClass->booked_places = $bookedCount;
            $masterClass->max_participants = $maxParticipants;
            
            // Добавляем отформатированную цену
            $masterClass->formatted_price = $masterClass->formatted_price;
            
            // Добавляем отформатированную дату
            $masterClass->formatted_next_event_date = $masterClass->formatted_next_event_date;
            
            return $masterClass;
        })->values();
        
        return response()->json($masterClasses);
    }

    /**
     * Store a newly created master class in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:master_classes',
            'price' => 'required|integer',
            'booked_places' => 'integer|min:0',
            'image_path' => 'nullable|string|max:255',
            'page_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:512',
            'max_participants' => 'integer|min:1',
            'is_active' => 'boolean',
            'tags' => 'nullable|array',
        ]);

        $masterClass = MasterClass::create($validated);

        return response()->json($masterClass, 201);
    }

    /**
     * Display the specified master class.
     *
     * @param  \App\Models\MasterClass  $masterClass
     * @return \Illuminate\Http\Response
     */
    public function show(MasterClass $masterClass)
    {
        return response()->json($masterClass);
    }

    /**
     * Update the specified master class in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasterClass  $masterClass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MasterClass $masterClass)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|unique:master_classes,slug,' . $masterClass->id,
            'price' => 'sometimes|required|integer',
            'booked_places' => 'integer|min:0',
            'image_path' => 'nullable|string|max:255',
            'page_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:512',
            'max_participants' => 'integer|min:1',
            'is_active' => 'boolean',
            'tags' => 'nullable|array',
        ]);

        $masterClass->update($validated);

        return response()->json($masterClass);
    }

    /**
     * Remove the specified master class from storage.
     *
     * @param  \App\Models\MasterClass  $masterClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterClass $masterClass)
    {
        $masterClass->delete();

        return response()->json(null, 204);
    }
}