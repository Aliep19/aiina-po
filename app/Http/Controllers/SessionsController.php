<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => 'required|string',
            'password' => 'required'
        ]);

        if (Auth::attempt([
            'name' => $attributes['name'],
            'password' => $attributes['password']
        ])) {
            request()->session()->regenerate();
            return redirect('/dashboard')->with('success', 'You are logged in.');
        }

        return back()->withErrors([
            'name' => 'Username or password invalid.'
        ]);
    }

    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success' => 'You\'ve been logged out.']);
    }
}
