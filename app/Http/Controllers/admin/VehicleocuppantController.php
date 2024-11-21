<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usertype;
use App\Models\Vehicle;
use App\Models\Vehicleocuppant;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleocuppantController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index($vehicleId, Request $request)
    {
        $vehicleName = Vehicle::find($vehicleId)->name;

        if ($request->ajax()) {
            $occupants = Vehicleocuppant::where('vehicle_id', $vehicleId)
                ->with(['user', 'usertype'])
                ->get();

            return DataTables::of($occupants)
                ->addColumn('usertype', function ($occupant) {
                    return $occupant->usertype->name;
                })
                ->addColumn('user', function ($occupant) {
                    return $occupant->user->name;
                })
                ->addColumn('status', function ($occupant) {
                    return $occupant->status ? '<div style="color: green"><i class="fas fa-check"></i> Activo</div>'
                        : '<div style="color: red"><i class="fas fa-times"></i> Inactivo</div>';
                })
                ->addColumn('actions', function ($occupant) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button class="dropdown-item btnEditar" id="' . $occupant->id . '"><i class="fas fa-edit"></i> Editar</button>
                                <form action="' . route('admin.vehicleocuppants.destroy', $occupant->id) . '" method="POST" class="frmEliminar d-inline">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.vehicleocuppants.index', compact('vehicleId', 'vehicleName'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create($vehicleId)
    {
        $users = User::where('usertype_id', '!=', null)->pluck('name', 'id');
        $usertypes = Usertype::pluck('name', 'id');

        $hasConductor = Vehicleocuppant::where('vehicle_id', $vehicleId)
            ->where('usertype_id', 2) // ID del tipo "Conductor"
            ->exists();
        $conductorTypeId = 2; // ID del tipo "Conductor"
        return view("admin.vehicleocuppants.create", compact("vehicleId", "users", "usertypes", "hasConductor", "conductorTypeId"));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $vehicleId)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'usertype_id' => 'required|exists:usertypes,id',
                'status' => 'boolean',
            ]);

            $existingConductor = Vehicleocuppant::where('vehicle_id', $vehicleId)
                ->where('usertype_id', 2)
                ->exists();

            if ($existingConductor && $request->usertype_id == 2) {
                return response()->json([
                    'message' => 'El vehículo ya tiene un conductor asignado',
                    'type' => 'info', // Indica que es informativo
                ], 200);
            }



            Vehicleocuppant::create([
                'vehicle_id' => $vehicleId,
                'user_id' => $request->user_id,
                'usertype_id' => $request->usertype_id,
                'status' => $request->status ?? 0,
            ]);

            return response()->json(['message' => 'Ocupante registrado correctamente'], 200);
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
    public function edit($id)
    {
        $occupant = Vehicleocuppant::findOrFail($id);
        $users = User::pluck('name', 'id');
        $usertypes = Usertype::pluck('name', 'id');

        return view('admin.vehicleocuppants.edit', compact('occupant', 'users', 'usertypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'usertype_id' => 'required|exists:usertypes,id',
                'status' => 'boolean',
            ]);

            $occupant = Vehicleocuppant::findOrFail($id);
            $occupant->update($request->only(['user_id', 'usertype_id', 'status']));

            return response()->json(['message' => 'Ocupante actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en la actualización: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $occupant = Vehicleocuppant::findOrFail($id);
            $occupant->delete();

            return response()->json(['message' => 'Ocupante eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en la eliminación: ' . $th->getMessage()], 500);
        }
    }
}
