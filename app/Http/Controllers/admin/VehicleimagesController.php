<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicleimage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleimagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function profile(string $id, string $vehicle_id)
    {
        try {
            DB::select("UPDATE vehicleimages SET profile=0 where vehicle_id=$vehicle_id");
            DB::select("UPDATE vehicleimages SET profile=1 where id=$id");
            return response()->json(['message' => 'Perfil actualizado correctamente'], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en la actualización: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $image = Vehicleimage::find($id);
            $image->delete();

            return response()->json(['message' => 'Imagen eliminada'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error de eliminación'], 500);
        }
    }
}
