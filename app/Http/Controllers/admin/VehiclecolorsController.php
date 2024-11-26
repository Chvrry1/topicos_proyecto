<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiclecolor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehiclecolorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehiclecolors = Vehiclecolor::all();

        if ($request->ajax()) {

            return DataTables::of($vehiclecolors)
                ->addColumn('actions', function ($vehiclecolor) {
                    return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>                        
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item btnEditar" id="' . $vehiclecolor->id . '"><i class="fas fa-edit"></i>  Editar</button>
                            <form action="' . route('admin.vehiclecolors.destroy', $vehiclecolor->id) . '" method="POST" class="frmEliminar d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                            </form>
                        </div>
                    </div>';
                })
                ->rawColumns(['actions'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.vehiclecolors.index', compact('vehiclecolors'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vehiclecolors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Vehiclecolor::create([
                'name' => $request->name,
                'description' => $request->description,
                'rgb_value' => $request->rgb_value // Guardar el valor RGB
            ]);
    
            return response()->json(['message' => 'Color de vehículo registrado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }

    }
    
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehiclecolor = Vehiclecolor::find($id);
        return view('admin.vehiclecolors.edit', compact('vehiclecolor'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vehiclecolor = Vehiclecolor::find($id);
            $vehiclecolor->update([
                'name' => $request->name,
                'description' => $request->description,
                'rgb_value' => $request->rgb_value // Actualizar el valor RGB
            ]);
    
            return response()->json(['message' => 'Color de vehículo actualizado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el color del vehículo: ' . $th->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vehiclecolor = Vehiclecolor::findOrFail($id);
            $vehiclecolor->delete();

            return response()->json(['message' => 'Color de vehículo eliminado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error de eliminación'], 500);
        }
    }
}
