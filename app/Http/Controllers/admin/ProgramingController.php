<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicleroutes;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Schedules;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class ProgramingController extends Controller
{
    public function index(Request $request)
    {
        $vehicle = Vehicle::pluck('name', 'id');
        $route = Route::pluck('name', 'id');
        $schedule = Schedules::pluck('name', 'id');

        $programing = Vehicleroutes::with('vehicle', 'route', 'schedule')->get();

        if ($request->ajax()) {

            return DataTables::of($programing)
                ->addColumn('actions', function ($programing) {
                    return '';
                })
                ->rawColumns(['actions'])
                ->make(true);
        } else {
            return view('admin.programing.index', compact('vehicle','route', 'schedule'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    

    public function store(Request $request)
    {
        try {
            Log::info('Iniciando el proceso de registro de programación.', ['request_data' => $request->all()]);

            // Validación de los datos recibidos
            $request->validate([
                'schedule_id'  => 'required|integer',
                'vehicle_id'   => 'required|integer',
                'route_id'     => 'required|integer',
                'date_start'   => 'required|date|after_or_equal:today',
                'date_end'     => 'required|date|after:date_start',
                'programming_type'  => 'required|string',
                'fines_type'    => 'required|string',
                'time_route'   => 'required|date_format:H:i',  // Validación para la hora de la ruta
            ]);

            Log::info('Validación completada.', ['validated_data' => $request->only(['schedule_id', 'vehicle_id', 'route_id', 'date_start', 'date_end', 'intercalado', 'sin_fines', 'time_route'])]);

            // Buscar el turno por el ID proporcionado
            $schedule = Schedules::find($request->schedule_id);

            if (!$schedule) {
                Log::error('El turno seleccionado no existe.', ['schedule_id' => $request->schedule_id]);
                return response()->json(['message' => 'El turno seleccionado no existe.'], 400);
            }

            Log::info('Turno encontrado.', ['schedule' => $schedule]);

            // Convertir los horarios de inicio y fin en formato de hora
            $schedule_time_start = $schedule->time_start;
            $schedule_time_end = $schedule->time_end;

            // Verificar que `time_route` esté dentro del rango de horarios del turno
            $time_route = $request->time_route;

            Log::info('Verificando la hora de la ruta.', [
                'time_route' => $time_route,
                'schedule_time_start' => $schedule_time_start,
                'schedule_time_end' => $schedule_time_end,
            ]);

            if ($time_route < $schedule_time_start || $time_route > $schedule_time_end) {
                Log::error('La hora de la ruta está fuera del rango del turno.', [
                    'time_route' => $time_route,
                    'schedule_time_start' => $schedule_time_start,
                    'schedule_time_end' => $schedule_time_end,
                ]);
                return response()->json([
                    'message' => 'La hora de la ruta debe estar dentro del rango del turno seleccionado.'
                ], 400);
            }

            // Ejecutar el procedimiento almacenado
            Log::info('Ejecutando el procedimiento almacenado para registrar la programación.', [
                'schedule_id' => $request->schedule_id,
                'vehicle_id' => $request->vehicle_id,
                'route_id' => $request->route_id,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'intercalado' => $request->programming_type,
                'sin_fines' => $request->fines_type,
                'time_route' => $request->time_route
            ]);

            DB::select('CALL sp_programing(?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->schedule_id,
                $request->vehicle_id,
                $request->route_id,
                $request->date_start,
                $request->date_end,
                $request->programming_type,
                $request->fines_type,
                $request->time_route  // Se pasa el tiempo de la ruta
            ]);

            Log::info('Programación registrada correctamente.');

            return response()->json(['message' => 'Programación registrada correctamente'], 200);

        } catch (\Throwable $th) {
            Log::error('Error en el registro de programación.', ['error_message' => $th->getMessage()]);
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',   
                'ids.*' => 'integer|exists:vehicleroutes,id', 
                'time_route' => 'nullable|date_format:H:i',
                'description' => 'nullable|string|max:500',
                'vehicle_id' => 'nullable|integer|exists:vehicles,id',
                'route_id' => 'nullable|integer|exists:routes,id', 
            ]);

            $data = $request->only(['time_route', 'description', 'vehicle_id', 'route_id']);

            if (empty($data)) {
                return response()->json(['message' => 'No se proporcionaron campos para actualizar'], 400);
            }

            if (isset($data['time_route'])) {
                // Obtener el turno del primer vehículo y ruta (suponemos que todos los registros tienen el mismo horario)
                $vehicleRoute = Vehicleroutes::find($request->ids[0]);
    
                $schedule = Schedules::find($vehicleRoute->schedule_id);
    
                if (!$schedule) {
                    return response()->json(['message' => 'El turno seleccionado no existe.'], 400);
                }
    
                $schedule_time_start = $schedule->time_start;
                $schedule_time_end = $schedule->time_end;
    
                $time_route = $data['time_route'];
    
                // Verificar si la hora de la ruta está dentro del rango del turno
                if ($time_route < $schedule_time_start || $time_route > $schedule_time_end) {
                    return response()->json([
                        'message' => 'La hora de la ruta debe estar dentro del rango del turno seleccionado.'
                    ], 400);
                }
            }

            $ids = $request->input('ids');

            $updated = Vehicleroutes::whereIn('id', $ids)->update($data);

            if ($updated) {
                return response()->json(['message' => 'Programación de rutas actualizada correctamente'], 200);
            } else {
                return response()->json(['message' => 'No se encontraron rutas para actualizar'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en la actualización: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            // Log de entrada: Verificamos los datos recibidos
            Log::info('Iniciando proceso de eliminación de sectores.', ['request_data' => $request->all()]);

            // Validación de los datos recibidos
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:vehicleroutes,id',
            ]);

            // Log después de la validación: Verificamos los ids que pasaron la validación
            Log::info('Ids de sectores a eliminar validados.', ['ids' => $request->ids]);

            // Realizamos la eliminación de los sectores
            $deletedCount = Vehicleroutes::whereIn('id', $request->ids)->delete();

            // Log para verificar cuántos sectores fueron eliminados
            Log::info('Programaciones eliminadas eliminados.', ['deleted_count' => $deletedCount]);

            if ($deletedCount > 0) {
                return response()->json(['message' => "$deletedCount programaciones eliminados correctamente"], 200);
            } else {
                Log::warning('No se encontraron programaciones para eliminar.', ['ids' => $request->ids]);
                return response()->json(['message' => 'No se encontraron programaciones para eliminar'], 404);
            }

        } catch (\Throwable $th) {
            // Log de error si ocurre una excepción
            Log::error('Error al eliminar las programaciones', [
                'error_message' => $th->getMessage(),
                'stack_trace' => $th->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Error al eliminar las programaciones: ' . $th->getMessage()], 500);
        }
    }
}
