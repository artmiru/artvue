<?php

namespace App\Http\Controllers;

use App\Models\ScheduleTemplate;
use Illuminate\Http\Request;

class ScheduleTemplateController extends Controller
{
    /**
     * Display a listing of the schedule templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = ScheduleTemplate::with('teacher')->get();
        return response()->json($templates);
    }

    /**
     * Store a newly created schedule template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:regular,master_class',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'teacher_id' => 'nullable|exists:teachers,id',
            'is_active' => 'boolean',
        ]);

        $template = ScheduleTemplate::create($validated);

        return response()->json($template, 201);
    }

    /**
     * Display the specified schedule template.
     *
     * @param  \App\Models\ScheduleTemplate  $template
     * @return \Illuminate\Http\Response
     */
    public function show(ScheduleTemplate $template)
    {
        $template->load('teacher');
        return response()->json($template);
    }

    /**
     * Update the specified schedule template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ScheduleTemplate  $template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ScheduleTemplate $template)
    {
        $validated = $request->validate([
            'type' => 'sometimes|required|in:regular,master_class',
            'day_of_week' => 'sometimes|required|integer|min:0|max:6',
            'start_time' => 'sometimes|required|date_format:H:i',
            'teacher_id' => 'nullable|exists:teachers,id',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return response()->json($template);
    }

    /**
     * Remove the specified schedule template from storage.
     *
     * @param  \App\Models\ScheduleTemplate  $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScheduleTemplate $template)
    {
        $template->delete();

        return response()->json(null, 204);
    }
}