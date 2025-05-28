<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/login', [App\Http\Controllers\EmployeeLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\EmployeeLoginController::class, 'login']);
Route::get('/dashboard', [App\Http\Controllers\PrintingShopContoller::class, 'PrintingShop'])->name('dashboard');

Route::get('/personnel', [App\Http\Controllers\PrintingShopContoller::class, 'Personnel'])->name('personnel');
Route::get('/employee', [App\Http\Controllers\PrintingShopContoller::class, 'EmployeeInfo'])->name('Employee Information');
Route::get('/addUser', [App\Http\Controllers\PrintingShopContoller::class, 'addUser'])->name('addUser');
Route::get('/employeeActivityLogs', [App\Http\Controllers\PrintingShopContoller::class, 'ActivityLogs'])->name('activityLogs');
Route::get('/profile', [App\Http\Controllers\PrintingShopContoller::class, 'profile'])->name('profile');

Route::post('/logout', [App\Http\Controllers\EmployeeLoginController::class, 'logout'])->name('logout');