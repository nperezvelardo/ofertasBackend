<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 20/08/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Clase controlador de Familia Profesional que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class FamprofController extends Controller {
    
    /**
	 * Método que obtiene de la base de datos el listado de familias profesionales
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function index(){

        $famprof = DB::table('famprof')->get();
        if(!empty($famprof)){
 
         $json = array(
 
             "status"=>200,
             "total_registros"=>count($famprof),
             "detalles"=>$famprof
             
         );
 
         }else{
 
             $json = array(
 
                 "status"=>200,
                 "total_registros"=>0,
                 "detalles"=>"No hay ninguna familia profesional registrada"
                 
             );
 
         }
 
         return json_encode($json, true);  //mandamos la información al cliente
 
    }
}
