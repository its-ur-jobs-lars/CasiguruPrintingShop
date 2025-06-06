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

Route::get('/orderDetails', [App\Http\Controllers\PrintingShopContoller::class, 'orderDetails'])->name('Order Details');
Route::get('/orderreceipts', [App\Http\Controllers\PrintingShopContoller::class, 'orderreceipts'])->name('Order Receipts');
Route::get('/orderdashboard', [App\Http\Controllers\PrintingShopContoller::class, 'orderDashboard'])->name('Order `Dashboard');
Route::get('/pricelist', [App\Http\Controllers\PrintingShopContoller::class, 'Pricelist'])->name('Price List');
Route::get('/category', [App\Http\Controllers\PrintingShopContoller::class, 'Category'])->name('Category');
Route::get('/subcategory', [App\Http\Controllers\PrintingShopContoller::class, 'SubCategory'])->name('SubCategory');
Route::get('/services', [App\Http\Controllers\PrintingShopContoller::class, 'Services'])->name('Services');
Route::get('/inventory', [App\Http\Controllers\PrintingShopContoller::class, 'Inventory'])->name('Inventory');


Route::post('/logout', [App\Http\Controllers\EmployeeLoginController::class, 'logout'])->name('logout');