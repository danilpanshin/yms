<?php

namespace App\Http\Controllers;

use App\Models\CarType;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request){


        return view('admin.index');
    }

    public function dictionary(){
        return view('admin.dictionary.index');
    }
}
