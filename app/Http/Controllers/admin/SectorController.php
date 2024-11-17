<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
            $sectors = Sector::all();

            if ($request->ajax()) {

                return DataTables::of($sectors)
                    ->addColumn('actions', function ($sector) {
                        return '
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bars"></i>                        
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <button class="dropdown-item btnEditar" id="' . $sector->id . '"><i class="fas fa-edit"></i>  Editar</button>
                                    <form action="' . route('admin.zones.destroy', $sector->id) . '" method="POST" class="frmEliminar d-inline">
                                        ' . csrf_field() . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                                    </form>
                                </div>
                            </div>';
                    })
                    ->addColumn('coords', function ($sector) {
                        return '<button class="btn btn-danger btn-sm btnMap" id='. $sector->id .'><i class="fas fa-map-marked-alt"></i></button>';
                    })
                    ->rawColumns(['actions', 'coords'])  // Declarar columnas que contienen HTML
                    ->make(true);
            } else {
                return view('admin.sectors.index', compact('sectors'));
            }
        
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
        $zones = collect(DB::select("CALL sp_zones(3,".$id.")"));
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
        return view('admin.sectors.show',compact('perimeter'));
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
