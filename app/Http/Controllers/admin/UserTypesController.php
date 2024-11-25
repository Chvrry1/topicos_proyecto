<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usertype;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UserTypesController extends Controller
{
    public function index(Request $request)
    {
        $usertypes = Usertype::all();

        if ($request->ajax()) {
            return DataTables::of($usertypes)->addColumn('actions', function ($usertype) {
                return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>                        
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item btnEditar" id="' . $usertype->id . '"><i class="fas fa-edit"></i>Editar</button>
                            <form action="' . route('admin.usertypes.destroy', $usertype->id) . '" method="POST" class="frmEliminar d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item btnEliminar" id="' . $usertype->id . '"><i class="fas fa-trash"></i> Eliminar</button>
                            </form>
                        </div>
                    </div>';
            })
            ->rawColumns(['actions'])->make(true);
        } else {
            return view('admin.usertypes.index', compact('usertypes'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.usertypes.form'); 
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:usertypes,name|alpha',
            'description' => 'max:50',
        ]);
    
        Usertype::create($validated);
    
        return response()->json(['message' => 'Tipo de personal creado exitosamente.']);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Usertype $usertype)
    {
        //
    }

    
    public function edit(Usertype $usertype)
    {
        return view('admin.usertypes.form', compact('usertype')); // Retorna el formulario con datos para editar

    }

    public function update(Request $request, Usertype $usertype)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('usertypes', 'name')->ignore($usertype->id), // Ignorar el registro actual
            ],
            'description' => 'nullable|max:50',
        ]);

        // Actualizar los datos del usuario
        $usertype->name = $validatedData['name'];
        $usertype->description = $validatedData['description'];

        // Guardar los cambios
        $usertype->save();

        // Retornar respuesta
        return response()->json([
            'message' => 'Tipo de personal actualizado correctamente.',
        ]);
    }

    public function destroy(Usertype $usertype)
    {
        // Verificar si existen usuarios con este tipo de usuario
        $usersWithThisUsertype = User::where('usertype_id', $usertype->id)->count();

        if ($usersWithThisUsertype > 0) {
            return response()->json(['error' => 'No se puede eliminar el tipo de usuario porque hay usuarios asociados a él.'], 400);
        }

        try {
            DB::beginTransaction();

            // Eliminar el tipo de usuario
            $usertype->delete();

            DB::commit();

            return response()->json(['message' => 'Tipo de personal eliminado exitosamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar el tipo de usuario.'], 500);
        }
    }


}
