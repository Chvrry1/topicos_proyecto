<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Brandmodel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BrandmodelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $models = Brandmodel::select(
            'brandmodels.id',
            'brandmodels.name',
            'b.name as bname',
            'brandmodels.description'
        )
            ->join('brands as b', 'brandmodels.brand_id', '=', 'b.id')->get();

        if ($request->ajax()) {

            return DataTables::of($models)
                ->addColumn('actions', function ($model) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i>                        
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button class="dropdown-item btnEditar" id="' . $model->id . '"><i class="fas fa-edit"></i>  Editar</button>
                                <form action="' . route('admin.models.destroy', $model->id) . '" method="POST" class="frmEliminar d-inline">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>';
                })
                ->rawColumns(['actions'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.models.index', compact('models'));
        }

        //return view('admin.models.index', compact('models'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::pluck('name', 'id');
        return view('admin.models.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Brandmodel::create($request->all());
            return response()->json(['message' => 'Modelo registrado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al registrar el modelo'], 500);
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
        $model = Brandmodel::find($id);
        $brands = Brand::pluck('name', 'id');
        return view('admin.models.edit', compact('model', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            $model = Brandmodel::find($id);
            $model->update($request->all());

            return response()->json(['message' => 'Modelo actualizado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el modelo'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $model = Brandmodel::find($id);
            $model->delete();
            return response()->json(['message' => 'Modelo eliminado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el modelo'], 500);
        }
    }

    public function modelsbybrand(string $id)
    {
        $models = Brandmodel::where("brand_id", $id)->get();
        return $models;
    }
}
