<?php

namespace App\Http\Controllers;

class AdminSettingsController extends Controller
{
    public function index(): string
    {
        return view('admin.settings.index');
    }


}
