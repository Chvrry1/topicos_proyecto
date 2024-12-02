<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usertype;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select(
            'users.id',
            'users.name',
            'users.dni',
            'ut.name as usertype_id',
            'z.name as zone_id'
        )
        ->join('usertypes as ut', 'users.usertype_id', '=', 'ut.id')
        ->leftjoin('zones as z', 'users.zone_id', '=', 'z.id')
        ->get();

        if ($request->ajax()) {

            return DataTables::of($users)
                ->addColumn('actions', function ($user) {
                    return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>                        
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item btnEditar" id="' . $user->id . '"><i class="fas fa-edit"></i>  Editar</button>
                            <form action="' . route('admin.users.destroy', $user->id) . '" method="POST" class="frmEliminar d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                            </form>
                        </div>
                    </div>';
                })
                ->rawColumns(['actions'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.users.index', compact('users'));
        }
    }

    public function filterByUsertype($usertypeId)
    {
        $users = User::where('usertype_id', $usertypeId)->pluck('name', 'id');
        return response()->json($users);
    }

    public function create()
    {
        $usertypes = Usertype::pluck('name', 'id');   
        $zones = Zone::pluck('name', 'id');           

        return view('admin.users.create', compact('usertypes', 'zones'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'dni' => 'required|numeric|digits:8|unique:users',
            'email' => 'unique:users',
            'usertype_id' => 'required|exists:usertypes,id',
            'zone_id' => 'required|exists:zones,id',
            'password' => 'required|string|confirmed',
            'license' => [
                'nullable',
                'string',
                'regex:/^[A-Za-z][0-9]{8}$/', 
                'required_if:usertype_id,2',
            ],
        ],[ 'license.regex' => 'El formato de la Licencia es inválido. El formato permitido es: XNNNNNNNN.',
            'license.required_if' =>'Usuario Conductor: Los Conductores deben Ingresar su Licencia.',
            'dni.unique' =>'Este DNI ya está registrado',
            'email.unique' =>'Este correo electronico ya está registrado',


    
        ]);      
        $license='';
        if ($request->usertype_id==2){
            $license=$request->license;
        }

        User::create([
            'name' => $request->name,
            'dni'=> $request->dni,
            'license' => $license,
            'email' => $request->email,
            'usertype_id' => $request->usertype_id,
            'zone_id' => $request->zone_id,
            'password' => bcrypt($request->password),
        ]);
        

        return response()->json(['message' => 'Usuario creado correctamente']);
    }
    public function show(string $id)
    {
        //
    }
    public function edit(string $id)
    {
        $user = User::find($id);
        $usertypes = Usertype::pluck('name', 'id');   
        $zones = Zone::pluck('name', 'id');           // Obtener las zonas

        return view('admin.users.edit', compact('user', 'usertypes', 'zones'));
    }

    public function update(Request $request, string $id)
{
    $user = User::find($id);

    $request->validate([
        'dni' => 'required|numeric|digits:8|unique:users,dni,' . $id,  // Ignore current user's DNI
        'email' => 'required|email|unique:users,email,' . $id,  // Ignore current user's email
        'usertype_id' => 'required|exists:usertypes,id',
        'zone_id' => 'required|exists:zones,id',
        'password' => 'nullable|string|confirmed',  // Allow nullable for password
        'license' => [
            'nullable',
            'string',
            'regex:/^[A-Za-z][0-9]{8}$/', 
            'required_if:usertype_id,2',
        ],
    ], [
        'license.regex' => 'El formato de la Licencia es inválido. El formato permitido es: XNNNNNNNN.',
        'license.required_if' => 'Usuario Conductor: Los Conductores deben Ingresar su Licencia.',
        'dni.unique' => 'Este DNI ya está registrado',
        'email.unique' => 'Este correo electronico ya está registrado',
    ]);

    $license = '';
    if ($request->usertype_id == 2) {
        $license = $request->license;
    }

    if ($request->password) {
        // If password is provided, update with the new one
        $user->update([
            'name' => $request->name,
            'dni' => $request->dni,
            'license' => $license,
            'email' => $request->email,
            'usertype_id' => $request->usertype_id,
            'zone_id' => $request->zone_id,
            'password' => bcrypt($request->password),
        ]);
    } else {
        // If password is not provided, don't update it
        $user->update([
            'name' => $request->name,
            'dni' => $request->dni,
            'license' => $license,
            'email' => $request->email,
            'usertype_id' => $request->usertype_id,
            'zone_id' => $request->zone_id,
        ]);
    }

    return response()->json(['message' => 'Persona actualizada'], 200);
}


    public function destroy(User $user)
    {
        try {
            // Intentar eliminar el usuario
            $user->delete();

            // Retornar una respuesta exitosa
            return response()->json([
                'message' => 'Usuario eliminado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            // Manejar errores
            return response()->json([
                'message' => 'No se pudo eliminar el usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
