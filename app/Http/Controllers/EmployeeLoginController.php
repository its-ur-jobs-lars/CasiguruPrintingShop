<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class EmployeeLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

public function login(Request $request)
{
    // Validate the request
    $request->validate([
        'employee_number' => 'required|string',
        'password' => 'required|string',
    ]);

    // Check if the user exists
    $user = \App\Models\User::where('employee_number', $request->employee_number)->first();

    // Handle inactive or non-existent user
    if (!$user) {
        return back()->with('withErrors', 'Account does not exist.')->withInput();
    }
    if ($user->isActive != 1) {
        return back()->with('withErrors', 'Your account is inactive.')->withInput();
    }

    // Attempt to log the user in
    if (Auth::attempt(['employee_number' => $request->employee_number, 'password' => $request->password])) {
        // Redirect to the intended page with a success message
        return redirect()->intended('/dashboard')->with('Login_Sucessfully', 'Login successful!');
    }

    // If login fails, redirect back with an error message
    return back()->with('withErrors', 'Unmatched credentials! Try again!')->withInput();
}

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect('/login');
    }
}
