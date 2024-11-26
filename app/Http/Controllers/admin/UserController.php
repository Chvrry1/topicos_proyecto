<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        //
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
