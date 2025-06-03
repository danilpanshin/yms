<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController
{

    public function index(){}

    public function login(){
        if(Auth::check()){
            return redirect()->route('index');
        }
        return view('auth.login');
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }

    public function auth(Request $request){
        if(Auth::check()){
            return redirect()->route('index');
        }
        $credentials = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);
        if (!Auth::attempt($credentials, 1)) {
            return view('auth.login');
        }

        return redirect()->intended('/');
    }

    public function profile(){
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }
}
