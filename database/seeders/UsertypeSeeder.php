<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Usertype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsertypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tipos de usuario
        $ut1 = new Usertype();
        $ut1->name = 'Administrador';
        $ut1->save();

        $ut2 = new Usertype();
        $ut2->name = 'Conductor';
        $ut2->save();

        $ut3 = new Usertype();
        $ut3->name = 'Recolector';
        $ut3->save();

        $ut4 = new Usertype();
        $ut4->name = 'Ciudadano';
        $ut4->save();

        // Asignar tipo de usuario a cada usuario
        $users = User::all(); 

        foreach ($users as $user) {
            $user->usertype_id = Usertype::inRandomOrder()->first()->id;
            $user->save();
        }
    }
}
