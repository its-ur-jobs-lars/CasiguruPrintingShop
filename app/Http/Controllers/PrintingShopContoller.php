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

    public function profile()
    {
        return view('profile');
    }

    public function EmployeeInfo()
    {
        return view('employee');
    }

    public function orderDetails()
    {
        return view('orderDetails');
    }

    public function Pricelist()
    {
        return view('priceList');
    }

    public function Category()
    {
        return view('category');
    }

    public function Services()
    {
        return view('services');
    }

    public function SubCategory()
    {
        return view('subCategory');
    }

    public function Inventory()
    {
        return view('inventory');
    }

    public function orderreceipts()
    {
        return view('orderReceipts');
    }

    public function orderDashboard()
    {
        return view('orderDashboard');
    }

    public function Payment()
    {
        return view('payment');
    }

    public function Sales()
    {
        return view('sales');
    }

    public function Report()
    {
        return view('reports_Orders');
    }

    



    

   
}
