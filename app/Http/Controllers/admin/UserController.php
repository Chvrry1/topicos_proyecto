<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usertype;
use App\Models\Zone;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['userType', 'zone'])->get();

        if ($request->ajax()) {
            return DataTables::of($users)
                ->addColumn('actions', function ($user) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i>                        
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button class="dropdown-item btnEditar" id="' . $user->id . '"><i class="fas fa-edit"></i>Editar</button>
                                <form action="' . route('admin.users.destroy', $user->id) . '" method="POST" class="frmEliminar d-inline">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="dropdown-item btnEliminar" id="' . $user->id . '"><i class="fas fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>';
                })
                ->addColumn('usertype_id', function ($user) {
                    return $user->userType ? $user->userType->name : ''; // Maneja valores null
                })
                ->addColumn('zone_id', function ($user) {
                    return $user->zone ? $user->zone->name : ''; // Maneja valores null
                })
                ->rawColumns(['actions'])
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
        $usertypes = Usertype::all(); // Obtener los tipos de usuario
        $zones = Zone::all(); // Obtener las zonas

        return view('admin.users.form', compact('usertypes', 'zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'usertype_id' => 'nullable|exists:usertypes,id',
            'zone_id'     => 'nullable|exists:zones,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'usertype_id' => $validated['usertype_id'] ?? null,
            'zone_id' => $validated['zone_id'] ?? null,
        ]);

        return response()->json(['message' => 'Usuario creado correctamente']);
    }


    public function show(string $id)
    {
        //
    }

    public function edit(User $user)
    {
        $usertypes = UserType::all();   // Obtener los tipos de usuario
        $zones = Zone::all();           // Obtener las zonas

        return view('admin.users.form', compact('user', 'usertypes', 'zones'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'usertype_id' => 'required|exists:usertypes,id',
            'zone_id' => 'nullable|exists:zones,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $request->filled('password') ? bcrypt($validated['password']) : $user->password,
            'usertype_id' => $validated['usertype_id'],
            'zone_id' => $validated['zone_id'] ?? null,
        ]);

        return response()->json(['message' => 'Usuario actualizado correctamente']);
    }


    public function destroy(User $user)
    {
        //
    }
}
