<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\VehicleocuppantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('', [AdminController::class, 'index'])->middleware('auth:sanctum');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::post('validate-field', [VehicleController::class, 'validateField'])->name('validate.field');
Route::get('admin/vehicleocuppants/{vehicle}', [VehicleocuppantController::class, 'index'])->name('admin.vehicleocuppants.index');
