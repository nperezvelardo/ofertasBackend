<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 21/08/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\Ofertas_ciclo;

/**
 * Clase controlador de los respectivos ciclos de cada oferta que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class Ofertas_cicloController extends Controller{
    
     /**
      * Método para mostrar los ciclos formativos de cada oferta
      *
      * @param  int  $id identificador de la oferta
      * @return \Illuminate\Http\Response  array con la información para el cliente
      */
      public function show($id){
        //buscamos los registro
        $ciclos = DB::table('ofertas_ciclo')
                    ->where('idO', '=', $id)->get();

        if(!empty($ciclos)){

            $json = array(

                "status"=>200,
                "detalles"=>$ciclos  //enviamos al cliente los codigos de los ciclos, allí debemos buscar los nombres según el código
                
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
      * Método para mostrar los ciclos formativos de cada oferta
      *
      * @param  int  $id identificador de la oferta
      * @return \Illuminate\Http\Response  array con la información para el cliente
      */
      public function showOferta($id){
        //buscamos los registro
        $ciclos = DB::table('ofertas_ciclo')
                    ->where('CodigoCiclo', '=', $id)->get();

        if(!empty($ciclos)){

            $idO = array();

            foreach($ciclos as $cic){
                array_push($idO, $cic->idO);
            }

            

            $json = array(

                "status"=>200,
                "detalles"=>$idO  //obtenemos las ofertas según el ciclo
                
            );

        }else{

            $json = array(

                "status"=>200,
                "detalles"=>"No hay ningún mensaje registrado"
                
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

        //buscamos el último id de la tabla ofertas
        $ofertas = DB::table('ofertas')->get();
        foreach( $ofertas as $ofe){
            $idO = $ofe->id;  //id de última oferta
        }

        if(!empty($usuario)){
            $ofe_ciclo = new Ofertas_ciclo();
            $ofe_ciclo->idO = $idO;
            $ofe_ciclo->CodigoCiclo = $ciclo->codigo;

            $ofe_ciclo->save();
 
             $json = array(
 
                 "status"=>200,
                 "detalles"=>$ofe_ciclo
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
 	 * Método que realiza la eliminación de un ciclo de la oferta
	 *
	 * @param  int  $idO identificador de la oferta
     * @param int $codigo identificador del ciclo
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
    public function destroy($idO, $codigo){
        //buscamos el registro para eliminarlo
        $usu_ciclo = Ofertas_ciclo::where("idO", $idO)->where("codigoCiclo", $codigo)->get();

        if(!empty($usu_ciclo)){  //comprobamos que el registro existe

            $usu_ciclo = Ofertas_ciclo::where("idO", $idO)->where("codigoCiclo", $codigo)->delete();

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
