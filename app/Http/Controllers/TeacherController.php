<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::with('user')->get();
        return response()->json($teachers);
    }

    /**
     * Store a newly created teacher in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'about' => 'required|string',
            'phone' => 'required|string|max:15',
            'folder' => 'required|string|max:100',
            'alt' => 'nullable|string|max:255',
            'keypass_code' => 'required|string|max:255',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json($teacher, 201);
    }

    /**
     * Display the specified teacher.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        $teacher->load('user');
        return response()->json($teacher);
    }

    /**
     * Update the specified teacher in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'about' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string|max:15',
            'folder' => 'sometimes|required|string|max:100',
            'alt' => 'nullable|string|max:255',
            'keypass_code' => 'sometimes|required|string|max:255',
        ]);

        $teacher->update($validated);

        return response()->json($teacher);
    }

    /**
     * Remove the specified teacher from storage.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->json(null, 204);
    }
}