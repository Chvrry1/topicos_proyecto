<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicleroutes extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relación con el modelo Vehicle
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relación con el modelo Route
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    // Relación con el modelo Schedules
    public function schedule()
    {
        return $this->belongsTo(Schedules::class);
    }
}
