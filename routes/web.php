<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return redirect()->route('students.index');
});

// Resource Route for Student CRUD
// Registers all required CRUD routes (index, store, show, update, destroy)
Route::resource('students', StudentController::class)->except(['create', 'edit']);