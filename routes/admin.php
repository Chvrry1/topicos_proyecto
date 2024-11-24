<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandmodelController;
use App\Http\Controllers\admin\SchedulesController;
use App\Http\Controllers\admin\SectorController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\VehiclecolorsController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\VehicleimagesController;
use App\Http\Controllers\admin\VehicleocuppantController;
use App\Http\Controllers\admin\VehicletypesController;
use App\Http\Controllers\admin\ZoneController;
use App\Http\Controllers\admin\ZonecoordController;

Route::resource('brands', BrandController::class)->names('admin.brands');
Route::resource('models', BrandmodelController::class)->names('admin.models');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
Route::resource('vehicletypes', VehicletypesController::class)->names('admin.vehicletypes');
Route::resource('vehiclecolors', VehiclecolorsController::class)->names('admin.vehiclecolors');
Route::resource('vehicleimages', VehicleimagesController::class)->names('admin.vehicleimages');
Route::get('modelsbybrand/{id}', [BrandmodelController::class, 'modelsbybrand'])->name('admin.modelsbybrand');
Route::get('imageprofile/{id}/{vehicle_id}', [VehicleimagesController::class, 'profile'])->name('admin.imageprofile');
Route::resource('zones', ZoneController::class)->names('admin.zones');
Route::resource('zonecoords', ZonecoordController::class)->names('admin.zonecoords');
Route::resource('sectors', SectorController::class)->names('admin.sectors');
Route::resource('vehicleocuppants', VehicleocuppantController::class)->names('admin.vehicleocuppants');
Route::get('vehicleocuppants/{vehicle}', [VehicleocuppantController::class, 'index'])->name('admin.vehicleocuppants.index');
Route::get('vehicleocuppants/create/{vehicleId}', [VehicleocuppantController::class, 'create'])->name('admin.vehicleocuppants.create');
Route::post('vehicleocuppants/store/{vehicleId}', [VehicleocuppantController::class, 'store'])->name('admin.vehicleocuppants.store');
Route::get('users/filter/{usertype_id}', [UserController::class, 'filterByUsertype'])->name('admin.users.filter');

Route::get('zonesbySector/{id}', [ZoneController::class, 'zonesbySector'])->name('admin.zonesbySector');
Route::resource('schedules', SchedulesController::class)->names('admin.schedules');