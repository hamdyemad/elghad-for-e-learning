<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LexaAdmin extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($folderName, $fileName)
    {
        $view = $folderName . "." . $fileName;
        
        // If it's the dashboard, fetch all required data
        if ($view === 'dashboard.index') {
            return $this->root();
        }

        // Render perticular view file by foldername and filename
        if (view()->exists($view)) {
            return view($view);
        }
        return abort(404);
    }

    public function root()
    {
        $studentsCount = \App\Models\User::where('type', 'student')->count();
        $instructorsCount = \App\Models\User::where('type', 'instructor')->count();
        $coursesCount = \App\Models\Course::count();
        $lessonsCount = \App\Models\Lesson::count();

        $recentCourses = \App\Models\Course::with('instructor')->latest('updated_at')->take(5)->get();
        $recentStudents = \App\Models\User::where('type', 'student')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'studentsCount', 'instructorsCount', 'coursesCount', 'lessonsCount',
            'recentCourses', 'recentStudents'
        ));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function live()
    {
        return "";
    }
}
