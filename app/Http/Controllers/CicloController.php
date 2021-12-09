<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 20/08/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Clase controlador de Ciclos Formativos que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class CicloController extends Controller{
    
    /**
	 * Método que obtiene de la base de datos el listado de ciclos formativos
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function index(){

        $ciclo = DB::table('ciclo')->get();
        if(!empty($ciclo)){
 
         $json = array(
 
             "status"=>200,
             "total_registros"=>count($ciclo),
             "detalles"=>$ciclo
             
         );
 
         }else{
 
             $json = array(
 
                 "status"=>200,
                 "total_registros"=>0,
                 "detalles"=>"No hay ningún ciclo formativo registrado"
                 
             );
 
         }
 
         return json_encode($json, true);  //mandamos la información al cliente
 
    }

    /**
      * Mostramos los recursos especificos de cada familia profesional
      *
      * @param  int  $codigoFam identificador de la familia Profesional del ciclo
      * @return \Illuminate\Http\Response  array con la información para el cliente
      */
      public function show($codigoFam){
        //buscamos los registro
        $ciclos = DB::table('ciclo')
                    ->where('codigoFam', '=', $codigoFam)->get();
        
        if(!empty($ciclos)){

            $json = array(

                "status"=>200,
                "detalles"=>$ciclos   //recorremos en el Cliente para mostrar los nombres de cada uno
                
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
      * Mostramos los recursos especificos de cada familia profesional
      *
      * @param  int  $codigoFam identificador de la familia Profesional del ciclo
      * @return \Illuminate\Http\Response  array con la información para el cliente
      */
      public function showNombre($codigo){
        //buscamos los registro
        $ciclos = DB::table('ciclo')
                    ->where('Codigo', '=', $codigo)->get();
        
        if(!empty($ciclos)){

            $json = array(

                "status"=>200,
                "detalles"=>$ciclos   //recorremos en el Cliente para mostrar los nombres de cada uno
                
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
     * Método que comprueba si el email existe en la base de datos para poder restablecer la contraseña
     *
     * @param Request $request
     * @return void
     */
    public function resCon(Request $request){

        $contraseña = Crypt::encryptString($request['password']);

        $usuario2 = DB::table('usuarios')->where('email', '=', $request['email'])->first();

        //comprobamos que existe el usuario
        if(!empty($usuario2)){
            //comprobamos que ese usuario esta activado
            if($usuario2->activo == 1){
                if($request['password'] == $request['password2']){ 
                    try{
                        $usuario = DB::table('usuarios')->where("email", $request['email'])->update(array(
                            'password' => $contraseña 
                        ));
                        //registramos la operacion en la tabla correspondiente
                        $logs = DB::table('logs')->insert(array(
                            'Usuario' => $usuario2->Usuario,
                            'Perfil' => $usuario2->perfil,
                            'Accion' => 'Restablecer contraseña',
                            'fecha' => NOW(),
                        ));
            
                    } catch (\Illuminate\Database\QueryException $exception) {
                        Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                        DB::rollBack();
                        return $exception;
                    }
    
                    DB::commit();              
                    
                    $json = array(

                        "status"=>200,
                        "detalles"=>$usuario2

                    );

                }else{
                    $json = array(
    
                        "status"=>402,
                        "detalles"=>"Las contraseñas son distintas"
                        
                    );
                }
                
            }else{
                $json = array(

                    "status"=>401,
                    "detalles"=>"El usuario no está activado"
                    
                );
            }

            

        }else{

            $json = array(

                "status"=>400,
                "detalles"=>"No hay ningun usuario registrado con esas credenciales"
                
            );

        }
        return json_encode($json, true);
    }
}
