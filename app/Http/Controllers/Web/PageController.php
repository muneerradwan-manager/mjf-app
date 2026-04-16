<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function dashboard()  { return view('dashboard'); }
    public function students()   { return view('students.index'); }
    public function teachers()   { return view('teachers.index'); }
    public function courses()    { return view('courses.index'); }
    public function classes()    { return view('classes.index'); }
    public function enrollments(){ return view('enrollments.index'); }
    public function assignments(){ return view('assignments.index'); }
    public function announcements(){ return view('announcements.index'); }
    public function events()     { return view('events.index'); }
}
