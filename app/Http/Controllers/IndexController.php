<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(){
        if(!Auth::check()){
            return redirect()->route('login');
        } else if(Auth::user()->hasRole('manager')){
            return redirect()->route('manager');
        } else if(Auth::user()->hasRole('stock_admin')){
            return redirect()->route('stock_admin');
        } else if(Auth::user()->hasRole('supplier')){
            return redirect()->route('supplier');
        } else if(Auth::user()->hasRole('admin')){
            return redirect()->route('admin');
        }

        return view('welcome');
    }
}
