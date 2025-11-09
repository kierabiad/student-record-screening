<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
    {
        // Fetch all students, ordered by creation date
        $students = Student::orderBy('created_at', 'desc')->get();
    
        return view('students.index', compact('students'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules as per the acceptance criteria
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:255|unique:students,student_id',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|max:255|unique:students,email',
            'course' => 'required|string|max:255',
            'year_level' => 'required|integer|min:1|max:6',
        ]);

        if ($validator->fails()) {
            // Flash a session key to automatically re-open the create modal on redirect
            return back()->withErrors($validator)->withInput()->with('show_create_modal', true);
        }

        Student::create($validator->validated());

        return redirect()->route('students.index')->with('success', 'Student record created successfully!');
    }

    /**
     * Display the specified resource.
     * Note: In this web app, this is used for a detailed view, served via AJAX (or directly in the view).
     */
    public function show(Student $student)
    {
        return response()->json($student);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        // Validation rules: Student ID and Email must be unique, but ignore the current student's own values.
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:255|unique:students,student_id,' . $student->id,
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|max:255|unique:students,email,' . $student->id,
            'course' => 'required|string|max:255',
            'year_level' => 'required|integer|min:1|max:6',
        ]);

        if ($validator->fails()) {
             // Flash a session key to re-open the edit modal for the specific student ID on redirect
            return back()->withErrors($validator)->withInput()->with('show_edit_modal_for_id', $student->id);
        }

        $student->update($validator->validated());

        return redirect()->route('students.index')->with('success', 'Student record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->route('students.index')->with('success', 'Student record deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting student record. Ensure there are no related records.');
        }
    }
}