<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $routes = DB::table('routes')
            ->select(
                'id',
                'name',
                'latitud_start',
                'latitude_end',
                'longitude_start',
                'longitude_end',
                'status'
            )
            ->get();

        if ($request->ajax()) {

            return DataTables::of($routes)
                ->addColumn('actions', function ($route) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i>                        
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button class="dropdown-item btnEditar" id="' . $route->id . '"><i class="fas fa-edit"></i>  Editar</button>
                                <form action="' . route('admin.routes.destroy', $route->id) . '" method="POST" class="frmEliminar d-inline">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>';
                })
                ->addColumn('coords', function ($route) {
                    return '<button class="btn btn-danger btn-sm btnMap" id='. $route->id .'><i class="fas fa-map-marked-alt"></i></button>';
                })
                ->rawColumns(['actions', 'coords'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.routes.index', compact('routes'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $zones = collect(DB::select("SELECT z.name AS zone, z2.latitude, z2.longitude FROM zones z INNER JOIN zonecoords z2 ON z.id = z2.zone_id;"));
        $groupedZones = $zones->groupBy("zone");
        $perimeter = $groupedZones->map(function($zone){
            $coords=$zone->map(function($item){
                return [
                    'lat'=>$item->latitude,
                    'lng'=>$item->longitude
                ];
            })->toArray();
            return [
                'name'=>$zone[0]->zone,
                'coords'=>$coords
            ];
        })->values();
        return view('admin.routes.create', compact('perimeter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Route::create([
                'name' => $request->name,
                'latitud_start' => $request->latitud_start,
                'longitude_start' => $request->longitude_start,
                'latitude_end' => $request->latitude_end,
                'longitude_end' => $request->longitude_end,
                'status' => $request->status ? 1 : 0,
            ]);
            return response()->json(['message' => 'Ruta registrado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $route = DB::table('routes')
            ->select(
                'name',
                'latitud_start',
                'latitude_end',
                'longitude_start',
                'longitude_end'
            )
            ->where('id', $id)
            ->first();

        return view('admin.routes.show', compact('route'));
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
