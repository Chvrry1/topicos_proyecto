<?php

namespace Database\Seeders;

use App\Models\Vehiclecolor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehiclecolorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $c1 = new Vehiclecolor();
        $c1->name = 'Blanco';
        $c1->save();

        $c2 = new Vehiclecolor();
        $c2->name = 'Rojo';
        $c2->save();

        $c3 = new Vehiclecolor();
        $c3->name = 'Negro';
        $c3->save();
    }
}
