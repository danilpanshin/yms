<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController
{

    public function index(){}

    public function login(){

        return view('auth.login');
    }

    public function auth(Request $request){
        if (!Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')], 1)) {
            return view('auth.login');
        }

        return redirect()->intended('/');
    }
}
