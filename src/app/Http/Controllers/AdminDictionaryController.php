<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDictionaryController extends Controller
{
    public function index(){
        return view('admin.dictionary.index');
    }
}
