<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\EmployeeActivityLogs; 
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

class EmployeeLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

// public function login(Request $request)
// {
//     // Validate the request
//     $request->validate([
//         'employee_number' => 'required|string',
//         'password' => 'required|string',
//     ]);

//     // Check if the user exists
//     $user = \App\Models\User::where('employee_number', $request->employee_number)->first();

//     // Handle inactive or non-existent user
//     if (!$user) {
//         return back()->with('withErrors', 'Account does not exist.')->withInput();
//     }
//     if ($user->isActive != 1) {
//         return back()->with('withErrors', 'Your account is inactive.')->withInput();
//     }

//     if (Auth::attempt(['employee_number' => $request->employee_number, 'password' => $request->password])) {
//         // Before logging new login, update any previous login logs for this user to isActive = 0
//         EmployeeActivityLogs::where('user_id', Auth::id())
//             ->where('isActive', 1)
//             ->where('action', 'login')
//             ->update(['isActive' => 0, 'action' => 'logout', 'description' => 'Auto-logout on new login']);

//         // Log the new login activity
//         EmployeeActivityLogs::create([
//             'user_id' => Auth::id(),
//             'user_name' => Auth::user()->username,
//             'action' => 'login',
//             'description' => 'User logged in',
//             'ip_address' => $request->ip(),
//             'isActive' => 1,
//         ]);
//         return redirect()->intended('/dashboard')->with('Login_Sucessfully', 'Login successful!');
//     }

//     // If login fails, redirect back with an error message
//     return back()->with('withErrors', 'Unmatched credentials! Try again!')->withInput();
// }

public function login(Request $request)
{
    $request->validate([
        'employee_number' => 'required|string',
        'password' => 'required|string',
    ]);

    $employeeNumber = $request->employee_number;
    $throttleKey = Str::lower('login:' . $employeeNumber);

    // Find the user
    $user = User::where('employee_number', $employeeNumber)->first();

    // If user not found
    if (!$user) {
        return back()->withErrors([
            'employee_number' => 'Account does not exist.'
        ])->withInput();
    }

    $maxAttempts = ($user->admin == 1) ? 5 : 3;

    // If user is already blocked
    if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
        // Set user as inactive if not already
        if ($user->isActive) {
            $user->isActive = 0;
            $user->save();
        }

        return back()->withErrors([
            'employee_number' => 'You have exceeded the maximum login attempts. Your account has been deactivated. Please contact the administrator.',
        ])->withInput();
    }

    // If user is inactive (after checking block above)
    if ($user->isActive != 1) {
        return back()->withErrors([
            'employee_number' => 'Your account is inactive.'
        ])->withInput();
    }

    // Attempt login
    if (Auth::attempt(['employee_number' => $employeeNumber, 'password' => $request->password])) {
        RateLimiter::clear($throttleKey); // Clear on success

        // Set all previous logins to inactive
        EmployeeActivityLogs::where('user_id', Auth::id())
            ->where('isActive', 1)
            ->where('action', 'login')
            ->update([
                'isActive' => 0,
                'action' => 'logout',
                'description' => 'Auto-logout on new login'
            ]);

        // Log current login
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

    // If login failed — add an attempt
    RateLimiter::hit($throttleKey, 300); // Lock for 5 mins

    $remaining = $maxAttempts - RateLimiter::attempts($throttleKey);

    // Auto-deactivate if max reached on this attempt
    if ($remaining <= 0) {
        $user->isActive = 0;
        $user->save();

        return back()->withErrors([
            'employee_number' => 'You have exceeded the maximum login attempts. Your account has been deactivated.',
        ])->withInput();
    }

    return back()->withErrors([
        'employee_number' => "Unmatched credentials! You have {$remaining} attempt" . ($remaining > 1 ? 's' : '') . " left.",
    ])->withInput();
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
