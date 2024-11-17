<?php

namespace Database\Seeders;

use App\Models\Vehicletype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicletypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $t1 = new Vehicletype();
        $t1->name = 'Carga lateral';
        $t1->save();

        $t2 = new Vehicletype();
        $t2->name = 'Carga trasera';
        $t2->save();

        $t3 = new Vehicletype();
        $t3->name = 'Carga trasera con grúa';
        $t3->save();
    }
}
