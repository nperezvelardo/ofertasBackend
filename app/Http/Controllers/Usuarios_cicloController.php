<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 21/08/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuarios_ciclo;

/**
 * Clase controlador de los respectivos ciclos de cada usuario que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class Usuarios_cicloController extends Controller{
    
    /**
      * Método para mostrar los ciclos formativos de cada usuario
      *
      * @param  int  $id identificador del usuario
      * @return \Illuminate\Http\Response  array con la información para el cliente
      */
      public function show($id){

        //buscamos los registro
        $ciclos = DB::table('usuarios_ciclo')
                    ->where('idU', '=', $id)->get();


        if(!empty($ciclos)){


            $json = array(

                "status"=>200,
                "detalles"=>$ciclos  //enviamos al cliente los nombres de los ciclos
                
            );

        }else{

            $json = array(

                "status"=>400,
                "detalles"=>"No hay ningún ciclo registrado"
                
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

    /**
      * Método para mostrar los ciclos formativos de cada usuario
      *
      * @param  int  $id identificador del usuario
      * @return \Illuminate\Http\Response  array con la información para el cliente
      */
      public function showU($id){

        //buscamos los registro
        $ciclos = DB::table('usuarios_ciclo')
                    ->where('CodigoCiclo', '=', $id)->get();


        if(!empty($ciclos)){


            $json = array(

                "status"=>200,
                "detalles"=>$ciclos  //enviamos al cliente los nombres de los ciclos
                
            );

        }else{

            $json = array(

                "status"=>400,
                "detalles"=>"No hay ningún ciclo registrado"
                
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

     /**
	 * Método que crea un registro con los datos que nos transfiere el cliente y lo almacena en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
    public function store(Request $request){

        //buscamos el código del ciclo que tenemos que añadir
        $cic = $request->input('codigo');
        $ciclo = DB::table('ciclo')->where('Nombre', $cic)->first();

        //buscamos el usuario que está añadiendo sus ciclos
        $usuario = DB::table('usuarios')->where('id', $request->input("idU"))->first();

        if(!empty($usuario)){
            $usu_ciclo = new Usuarios_ciclo();
            $usu_ciclo->idU = $usuario->id;
            $usu_ciclo->CodigoCiclo = $ciclo->codigo;

            $usu_ciclo->save();
 
             $json = array(
 
                 "status"=>200,
                 "detalles"=>$usu_ciclo
             );
 
             return json_encode($json, true);  //mandamos la información al cliente
 
         }else{
 
             $json = array(
 
                 "status"=>404,
                 "detalles"=>"Los registros no pueden estar vacíos"
             
             );
 
         }
         return json_encode($json, true);  //mandamos la información al cliente
    }

    /**
 	 * Método que realiza la eliminación de un ciclo del usuario
	 *
	 * @param  int  $idU identificador del usuario
     * @param int $codigo identificador del ciclo
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
    public function destroy($idU, $codigo){
        //buscamos el registro para eliminarlo
        $usu_ciclo = Usuarios_ciclo::where("idU", $idU)->where("codigoCiclo", $codigo)->get();

        if(!empty($usu_ciclo)){  //comprobamos que el registro existe

            $usu_ciclo = Usuarios_ciclo::where("idU", $idU)->where("codigoCiclo", $codigo)->delete();

            $json = array(

                "status"=>200,
                "detalles"=>"Se ha borrado su ciclo con exito"
                
            );

        }else{   //si no se encuentra se lo comunicamos al cliente

            $json = array( 

                "status"=>404,
                "detalles"=>"El ciclo no existe"
            );


        }
        return json_encode($json, true);  //mandamos la información al cliente
    }
}
