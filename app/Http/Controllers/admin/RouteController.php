<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteZone;
use App\Models\Sector;
use App\Models\Zone;
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
        $zones = DB::table('zones')
            ->select('id', 'name')
            ->get(); // Obtener todas las zonas
        return view('admin.routes.create', compact('zones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $route =Route::create([
                'name' => $request->name,
                'latitud_start' => $request->latitud_start,
                'longitude_start' => $request->longitude_start,
                'latitude_end' => $request->latitude_end,
                'longitude_end' => $request->longitude_end,
                'status' => $request->status ? 1 : 0,
            ]);
            if ($request->has('zone') && is_array($request->zone)) {
                foreach ($request->zone as $zoneId) {
                    RouteZone::create([
                        'route_id' => $route->id,
                        'zone_id' => $zoneId,
                    ]);
                }
            }
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
        $route = Route::find($id);

        $zoneIds = DB::table('routezones')
                    ->where('route_id', $id)
                    ->pluck('zone_id')
                    ->toArray();

        $zonesWithCoords = DB::table('zones')
                            ->join('zonecoords', 'zones.id', '=', 'zonecoords.zone_id')
                            ->whereIn('zones.id', $zoneIds)
                            ->select('zones.id as zone_id', 'zones.name', 'zonecoords.latitude', 'zonecoords.longitude')
                            ->get()
                            ->groupBy('zone_id'); // Agrupar por zona

        return view('admin.routes.show', compact('route', 'zonesWithCoords'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $route = Route::find($id);

        $selectedZones = DB::table('routezones')
                            ->where('route_id', $id)
                            ->pluck('zone_id')
                            ->toArray();  

        $zones = collect(DB::select("SELECT z.id, z.name FROM zones z"));

        $latitud_start = $route->latitud_start;
        $longitude_start = $route->longitude_start;
        $latitude_end = $route->latitude_end;
        $longitude_end = $route->longitude_end;

        return view('admin.routes.edit', compact('latitud_start', 'longitude_start', 'latitude_end', 'longitude_end', 'zones', 'selectedZones','route'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $route = Route::find($id); // Buscar la ruta por ID

            $route->name = $request->name;
            $route->latitud_start = $request->latitud_start;
            $route->longitude_start = $request->longitude_start;
            $route->latitude_end = $request->latitude_end;
            $route->longitude_end = $request->longitude_end;
            $route->status = $request->status ? 1 : 0;

            $route->save(); // Guardar los cambios en la base de datos

            if ($request->has('zone') && is_array($request->zone)) {
                RouteZone::where('route_id', $route->id)->delete();

                foreach ($request->zone as $zoneId) {
                    RouteZone::create([
                        'route_id' => $route->id,
                        'zone_id' => $zoneId,
                    ]);
                }
            }

            return response()->json(['message' => 'Ruta actualizada con éxito'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar la ruta: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $route = Route::findOrFail($id); // Buscar la ruta por ID

            $hasZones = DB::table('routezones')->where('route_id', $route->id)->exists();
            if ($hasZones) {
                return response()->json(['message' => 'No se puede eliminar la ruta, ya que tiene zonas asociadas.'], 400);
            }

            $route->delete();

            return response()->json(['message' => 'Ruta eliminada con éxito'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar la ruta: ' . $th->getMessage()], 500);
        }
    }

}
