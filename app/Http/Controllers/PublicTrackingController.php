<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicTrackingController extends Controller
{
    public function show()
    {
        return view('public.track');
    }
}
