<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrintingShopContoller extends Controller
{
    public function PrintingShop()
    {
        return view('dashboard');
    }

    public function Personnel()
    {
        return view('personnel');
    }

    public function addUser()
    {
        return view('addUser');
    }

    public function ActivityLogs()
    {
        return view('employeeActivityLogs');
    }

   
}
