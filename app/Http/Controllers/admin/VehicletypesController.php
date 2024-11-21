<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicletype;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicletypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehicletypes = Vehicletype::all();

        if ($request->ajax()) {

            return DataTables::of($vehicletypes)

                ->addColumn('actions', function ($vehicletype) {
                    return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>                        
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item btnEditar" id="' . $vehicletype->id . '"><i class="fas fa-edit"></i>  Editar</button>
                            <form action="' . route('admin.vehicletypes.destroy', $vehicletype->id) . '" method="POST" class="frmEliminar d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                            </form>
                        </div>
                    </div>';
                })
                ->rawColumns(['actions'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.vehicletypes.index', compact('vehicletypes'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vehicletypes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Vehicletype::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json(['message' => 'Tipo de vehículo registrado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }
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
        $vehicletype = Vehicletype::find($id);
        return view('admin.vehicletypes.edit', compact('vehicletype'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $vehicletype = Vehicletype::find($id);
            $vehicletype->update($request->all());

            return response()->json(['message' => 'Tipo de vehículo actualizado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el tipo de vehículo'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vehicletype = Vehicletype::find($id);
            $vehicletype->delete();

            return response()->json(['message' => 'Tipo de vehículo eliminado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error de eliminación'], 500);
        }
    }
}
