<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Persona;
use App\Models\Proveedore;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        //$criterio = $request->criterio;
         
        if ($buscar==''){
            $personas = User::join('personas','users.id', '=', 'personas.id')
            ->join('roles','users.idrol', '=', 'roles.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
            'personas.num_documento', 'personas.direccion', 'personas.telefono',
            'personas.email', 'users.usuario', 'users.password',
            'users.condicion', 'users.idrol', 'roles.nombre as rol')
            ->orderBy('personas.id', 'desc')->paginate(3);
        }
        else{
            $personas = User::join('personas','users.id', '=', 'personas.id')
            ->join('roles','users.idrol', '=', 'roles.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
            'personas.num_documento', 'personas.direccion', 'personas.telefono',
            'personas.email', 'users.usuario', 'users.password',
            'users.condicion', 'users.idrol'. 'roles.nombre as rol')
            ->where('personas.nombre', 'like', '%'. $buscar . '%')
            ->orWhere('personas.num_documento', 'like', '%'. $buscar . '%')
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

            $user = new User();
            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);
            $user->condicion = '1';
            $user->idrol = $request->idrol;
            
            $user->id = $persona->id;
            $user->save();

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

            $user = User::findOrFail($request->id);
            $persona = Persona::findOrFail($user->id);

            
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();

  
            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);
            $user->condicion = '1';
            $user->idrol = $request->idrol;

            $user->save();

            DB::commit();

        } catch (Exception $e){
            DB::rollBack();
        }

    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '1';
        $user->save();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '0';
        $user->save();
    }



}
