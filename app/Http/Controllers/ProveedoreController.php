<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Proveedore;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class ProveedoreController extends Controller
{

    public function index(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        //$criterio = $request->criterio;
         
        if ($buscar==''){
            $personas = Proveedore::join('personas','proveedores.id', '=', 'personas.id')
                                     ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
                                     'personas.num_documento', 'personas.direccion', 'personas.telefono',
                                     'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
                                    ->orderBy('personas.id', 'desc')->paginate(3);
        }
        else{
            $personas = Proveedore::join('personas','proveedores.id', '=', 'personas.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
            'personas.num_documento', 'personas.direccion', 'personas.telefono',
            'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
            ->where('personas.nombre', 'like', '%'. $buscar . '%')
            ->orWhere('personas.num_documento', 'like', '%'. $buscar . '%')
            ->orWhere('personas.direccion', 'like', '%'. $buscar . '%')
            ->orWhere('personas.telefono', 'like', '%'. $buscar . '%')
            ->orWhere('personas.email', 'like', '%'. $buscar . '%')
            ->orderBy('personas.id', 'desc')->paginate(3);
        }
         
 
        return [
            'pagination' => [
                'total'        => $personas->total(),
                'current_page' => $personas->currentPage(),
                'per_page'     => $personas->perPage(),
                'last_page'    => $personas->lastPage(),
                'from'         => $personas->firstItem(),
                'to'           => $personas->lastItem(),
            ],
            'personas' => $personas
        ];
    }

    public function selectProveedor(Request $request){
        //if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $proveedores = Proveedore::join('personas','proveedores.id','=','personas.id')
        ->where('personas.nombre', 'like', '%'. $filtro . '%')
        ->orWhere('personas.num_documento', 'like', '%'. $filtro . '%')
        ->select('personas.id','personas.nombre','personas.num_documento')
        ->orderBy('personas.nombre', 'asc')->get();

        return ['proveedores' => $proveedores];
    }


    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            
            DB::beginTransaction();
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();

            $proveedor = new Proveedore();
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->id = $persona->id;
            $proveedor->save();

            DB::commit();

        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            
            DB::beginTransaction();

            $proveedor = Proveedore::findOrFail($request->id);
            $persona = Persona::findOrFail($proveedor->id);

            
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();

  
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->save();

            DB::commit();

        } catch (Exception $e){
            DB::rollBack();
        }

    }

}
