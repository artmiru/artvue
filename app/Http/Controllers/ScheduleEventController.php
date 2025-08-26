<?php

namespace App\Http\Controllers;

use App\Models\ScheduleEvent;
use Illuminate\Http\Request;

class ScheduleEventController extends Controller
{
    /**
     * Display a listing of the schedule events.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = ScheduleEvent::with(['template', 'teacher', 'masterClass'])->get();
        return response()->json($events);
    }

    /**
     * Store a newly created schedule event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'template_id' => 'nullable|exists:schedule_templates,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'master_class_id' => 'nullable|exists:master_classes,id',
            'booked_count' => 'integer|min:0',
            'max_participants' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        $event = ScheduleEvent::create($validated);

        return response()->json($event, 201);
    }

    /**
     * Display the specified schedule event.
     *
     * @param  \App\Models\ScheduleEvent  $event
     * @return \Illuminate\Http\Response
     */
    public function show(ScheduleEvent $event)
    {
        $event->load(['template', 'teacher', 'masterClass']);
        return response()->json($event);
    }

    /**
     * Update the specified schedule event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ScheduleEvent  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ScheduleEvent $event)
    {
        $validated = $request->validate([
            'start_datetime' => 'sometimes|required|date',
            'template_id' => 'nullable|exists:schedule_templates,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'master_class_id' => 'nullable|exists:master_classes,id',
            'booked_count' => 'integer|min:0',
            'max_participants' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    /**
     * Remove the specified schedule event from storage.
     *
     * @param  \App\Models\ScheduleEvent  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScheduleEvent $event)
    {
        $event->delete();

        return response()->json(null, 204);
    }
}