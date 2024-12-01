<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Zonecoord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZonecoordController extends Controller{
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
        try {
            $coordinates = json_decode($request->input('coordinates'));

            foreach ($coordinates as $coordinate) {
                Zonecoord::create([
                    'zone_id' => $request->input('zone_id'),
                    'latitude' => $coordinate->lat,
                    'longitude' => $coordinate->lng,
                ]);
            }

            return response()->json(['message' => 'Coordenadas registradas'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    
     public function show(string $id)
    {
        $vertice = Zonecoord::select(
            'latitude as lat',
            'longitude as lng'
        )->where('zone_id', $id)->get();
        
        return view('admin.zonecoords.show',compact('vertice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    //modificar este
    public function edit(string $id)
    {
        $lastCoords = Zonecoord::select(
            'latitude as lat',
            'longitude as lng'
        )->where('zone_id', $id)->latest()->first();

        $vertice = Zonecoord::select(
            'latitude as lat',
            'longitude as lng'
        )->where('zone_id', $id)->get();

        // Obtener todas las zonas existentes
        
        $existingZones = Zonecoord::select('zone_id', 'latitude as lat', 'longitude as lng')
            ->where('zone_id', '!=', $id)
            ->get()
            ->groupBy('zone_id')
            ->map(function ($zone) {
                return $zone->map(function ($coord) {
                    return ['lat' => $coord->lat, 'lng' => $coord->lng];
                })->toArray();
        });

        return view('admin.zonecoords.create', compact('lastCoords', 'vertice', 'existingZones'))->with('zone_id', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $zonecoord = Zonecoord::find($id);
            $zonecoord->delete();
            return response()->json(['message' => 'Coordenada eliminada'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en la eliminación: ' . $th->getMessage()], 500);
        }
    }
    
}