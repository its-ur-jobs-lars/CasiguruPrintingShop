<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\EmployeeActivityLogs; 

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

    if (Auth::attempt(['employee_number' => $request->employee_number, 'password' => $request->password])) {
        // Before logging new login, update any previous login logs for this user to isActive = 0
        EmployeeActivityLogs::where('user_id', Auth::id())
            ->where('isActive', 1)
            ->where('action', 'login')
            ->update(['isActive' => 0, 'action' => 'logout', 'description' => 'Auto-logout on new login']);

        // Log the new login activity
        EmployeeActivityLogs::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->username,
            'action' => 'login',
            'description' => 'User logged in',
            'ip_address' => $request->ip(),
            'isActive' => 1,
        ]);
        return redirect()->intended('/dashboard')->with('Login_Sucessfully', 'Login successful!');
    }

    // If login fails, redirect back with an error message
    return back()->with('withErrors', 'Unmatched credentials! Try again!')->withInput();
}

    public function logout(Request $request)
    {
        EmployeeActivityLogs::create([
        'user_id' => Auth::id(),
        'user_name' => Auth::user()->username,
        'action' => 'logout',
        'description' => 'User logged out',
        'ip_address' => $request->ip(),
        'isActive' => 0,
    ]);
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
      
    }
}
