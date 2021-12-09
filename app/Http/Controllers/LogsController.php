<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 17/08/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Logs;
use PDF;

/**
 * Clase controlador de Logs que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class LogsController extends Controller{

    /**
	 * Método que obtiene de la base de datos el listado de logs
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function index(){
        
        if(isset($_GET["page"])){
            $logs = DB::table('logs')->paginate(4);
        }else{
            $logs = DB::table('logs')->get();
        }

        if(!empty($logs)){

            $json = array(

                "status"=>200,
                "total_registros"=>count($logs),
                "detalles"=>$logs
                
            );

        }else{

            $json = array(

                "status"=>200,
                "total_registros"=>0,
                "detalles"=>"No hay ninguna operacion registrada"
                
            );

        }

        return json_encode($json, true);  //mandamos la información al cliente
    }


    /**
     * Método que elimina todas las operaciones registradas.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(){

        //recogemos todas las operaciones que se encuentran en la base de datos
        $logs = DB::table('logs')->get();

        if(!empty($logs)){  //comprobamos que existe alguna operacion
            try{
                $logs = DB::table('logs')->delete();
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalle"=>"Se ha borrado sus operaciones con exito"

            );
        }else{

            $json = array(

                "status"=>404,
                "detalle"=>"No hay ninguna operacion registrada"
            );


        }
        return json_encode($json, true);   //mandamos la información al cliente
    }

    /**
     * Método que elimina una operacion registrada.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyLog($id){

        //buscamos la operación a eliminar
        $logs = Logs::where("id", $id)->get();

        if(!empty($logs)){  //comprobamos que existe alguna operacion
            try{
                $logs = Logs::where("id", $id)->delete();
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalle"=>"Se ha borrado la operacion con exito"

            );

        }else{

            $json = array(

                "status"=>404,
                "detalle"=>"No hay ninguna operacion registrada"
            );


        }
        return json_encode($json, true);   //mandamos la información al cliente
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datos = array( "accion"=>$request->input("accion"),
                "usuario"=>$request->input("usuario"),
                "perfil"=>$request->input("perfil"));

        if(!empty($datos)){
            $logs = new Logs();
            $logs->Usuario= $datos["usuario"];
            $logs->Perfil= $datos["perfil"];
            $logs->Accion= $datos["accion"];
            $logs->fecha = NOW();

            $logs->save();

            $json = array(

                "status"=>200,
                "detalles"=>"Operacion creada"
                
            );
        }else{
            $json = array(

                "status"=>404,
                "detalles"=>"Operacion no creada"
                
            );
        }

        return json_encode($json, true);
    }
    
    /**
    * Funcion para imprimir en pdf todo el listado de logs disponibles en la base de datos
    */
    public function imprimir(){
        $logs = DB::table('logs')->get();
        $pdf = \PDF::loadView('index', compact('logs'));
        return $pdf->download('imprimir.pdf');
    }
}
