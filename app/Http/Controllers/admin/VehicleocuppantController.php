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
        $vehicle = Vehicle::findOrFail($vehicleId); // Obtener el vehículo
        $vehicleName = $vehicle->name;
        $currentOccupants = Vehicleocuppant::where('vehicle_id', $vehicleId)->count();

        // Verificar si se alcanzó la capacidad máxima
        $capacityReachedMessage = null;
        $disableNewButton = false;

        if ($currentOccupants >= $vehicle->occupant_capacity) {
            $capacityReachedMessage = 'El vehículo ya alcanzó su capacidad máxima de ' . $vehicle->occupant_capacity . ' ocupantes.';
            $disableNewButton = true; // Por defecto, deshabilitar el botón

            // Verificar si falta un conductor
            $hasConductor = Vehicleocuppant::where('vehicle_id', $vehicleId)
                ->where('usertype_id', 2) // 2: ID del tipo "Conductor"
                ->exists();

            if (!$hasConductor) {
                $disableNewButton = false; // Habilitar el botón para permitir registrar un conductor
            }
        }

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
                                <button class="dropdown-item btnToggleStatus" data-id="' . $occupant->id . '"><i class="fas fa-sync"></i> Cambio de Estado</button>
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

        return view('admin.vehicleocuppants.index', compact(
            'vehicleId',
            'vehicleName',
            'capacityReachedMessage',
            'disableNewButton'
        ))->with('maxCapacity', $vehicle->occupant_capacity);

    }

    public function toggleStatus($id)
    {
        try {
            $occupant = Vehicleocuppant::findOrFail($id);
            $occupant->status = !$occupant->status; // Alternar el estado
            $occupant->save();

            $newStatus = $occupant->status ? 'Activo' : 'Inactivo';
            return response()->json(['message' => "El estado ha sido cambiado a {$newStatus}."], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al cambiar el estado: ' . $th->getMessage()], 500);
        }
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create($vehicleId)
    {
        $users = User::where('usertype_id', '!=', null)->pluck('name', 'id');
        $usertypes = Usertype::whereIn('id', [2, 3])->pluck('name', 'id'); // Filtrar solo Conductor y Recolector

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
    
            $vehicle = Vehicle::findOrFail($vehicleId);
            $currentOccupants = Vehicleocuppant::where('vehicle_id', $vehicleId)->count();
            $existingConductor = Vehicleocuppant::where('vehicle_id', $vehicleId)
                ->where('usertype_id', 2) // 2: Tipo Conductor
                ->exists();
    
            // Validar si el usuario ya está registrado en este vehículo
            $userInThisVehicle = Vehicleocuppant::where('vehicle_id', $vehicleId)
                ->where('user_id', $request->user_id)
                ->exists();
    
            if ($userInThisVehicle) {
                return response()->json([
                    'message' => 'El usuario ya está registrado como ocupante en este vehículo.',
                    'type' => 'warning',
                ], 400);
            }
    
            // Validar si el usuario ya está registrado en cualquier vehículo
            $userInAnyVehicle = Vehicleocuppant::where('user_id', $request->user_id)
                ->exists();
    
            if ($userInAnyVehicle) {
                return response()->json([
                    'message' => 'El usuario ya está registrado como ocupante en otro vehículo.',
                    'type' => 'warning',
                ], 400);
            }
    
            // Validar si se alcanza la capacidad máxima sin conductor
            if ($currentOccupants + 1 >= $vehicle->occupant_capacity && !$existingConductor && $request->usertype_id != 2) {
                return response()->json([
                    'message' => 'Se requiere que el último ocupante registrado sea un conductor.',
                    'disableNewButton' => true,
                    'type' => 'warning',
                ], 400);
            }
    
            // Validar capacidad máxima
            if ($currentOccupants >= $vehicle->occupant_capacity) {
                return response()->json([
                    'message' => 'No se pueden registrar más ocupantes. El vehículo ya alcanzó su capacidad máxima.',
                    'disableNewButton' => true,
                    'type' => 'warning',
                ], 400);
            }
    
            // Registrar ocupante
            Vehicleocuppant::create([
                'vehicle_id' => $vehicleId,
                'user_id' => $request->user_id,
                'usertype_id' => $request->usertype_id,
                'status' => $request->status ?? 0,
            ]);
    
            $currentOccupants += 1;
    
            // Determinar si el botón debe deshabilitarse
            $disableNewButton = $currentOccupants >= $vehicle->occupant_capacity;
    
            return response()->json([
                'message' => 'Ocupante registrado correctamente.',
                'disableNewButton' => $disableNewButton,
                'type' => 'success',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error en el registro: ' . $th->getMessage(),
                'type' => 'error',
            ], 500);
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
        $vehicleoccupant = Vehicleocuppant::findOrFail($id);
        $users = User::pluck('name', 'id');
        $usertypes = Usertype::pluck('name', 'id');

        return view('admin.vehicleocuppants.edit', compact('vehicleoccupant', 'users', 'usertypes'));
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
