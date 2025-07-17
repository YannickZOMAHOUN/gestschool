<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(404, "Aucun élève lié à ce compte parent.");
        }

        $recording = $student->recordings()->latest()->first();

        return view('parents.dashboard', compact('student', 'recording'));
    }
}
