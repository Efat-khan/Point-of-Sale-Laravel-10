<?php

namespace App\Http\Controllers\demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class demoController extends Controller
{
    public function index()
    {
        return view('about');
    }

    public function indexContact(){
        return view('contact');
    }

}
