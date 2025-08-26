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
        $masterClasses = MasterClass::all();
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