<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 17/08/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mensajes;

/**
 * Clase controlador de Mensajes que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class MensajesController extends Controller{

    /**
	 * Método que obtiene de la base de datos el listado de mensajes
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function index(){

        $mensajes = DB::table('mensajes')->get();
        if(!empty($mensajes)){
 
         $json = array(
 
             "status"=>200,
             "total_registros"=>count($mensajes),
             "detalles"=>$mensajes
             
         );
 
         }else{
 
             $json = array(
 
                 "status"=>200,
                 "total_registros"=>0,
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
 
        //preparar este metodo para poder seleccionar varios usuarios y enviar el mensaje a los usuarios seleccionados
         //Buscamos el usuario al que le vamos a enviar el mensaje mediante un select
 
         //Recoger datos
         $datos = array( "titulo"=>$request->input("titulo"),
                 "contenido"=>$request->input("contenido"),
                 "usuario" =>$request->input("usuario"),
                 "destino" =>$request->input("destino"),
         );
 
         //falta recoger los ciclos de la oferta e insertarlos en la tabla ofertas_ciclo
 
         //comprobamos que existen datos
         if(!empty($datos)){
             $mensajes = new Mensajes();
             $mensajes->titulo = $datos["titulo"];
             $mensajes->contenido = $datos["contenido"];
             $$mensajes->usuario = $datos["usuario"];
             $mensajes->fecha = NOW();
             $mensajes->destino = $datos["destino"];
 
             $mensajes->save();
 
             //registramos la operacion en la tabla base de datos
             $logs = DB::table('logs')->insert(array(
                 'Usuario' => 'noe86',  //extraer el nombre de usuario para insertarlo
                 'Perfil' => 'Admin', //el usuario puede enviar mensajes, comprobar que perfil tiene
                 'Accion' => 'Mensaje enviado',
                 'fecha' => NOW(),
             ));
 
             $json = array(
 
                 "status"=>200,
                 "detalles"=>$mensajes
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
      * Mostramos un recurso especifico. Mediante su identificador buscamos el registro para mostrar todos los datos
      *
      * @param  int  $id identificador del mensaje
      * @return \Illuminate\Http\Response  array con la información para el cliente
      */
     public function show($id){
         //buscamos el registro
         $mensajes = DB::table('mensajes')
                     ->where('id', '=', $id)->first();
         
         if(!empty($mensajes)){
 
             $json = array(
 
                 "status"=>200,
                 "detalles"=>$mensajes
                 
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
 	 * Método que realiza la eliminación de un mensaje a través del campo id
	 *
	 * @param  int  $id identificador del recurso a eliminar
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
     public function destroy($id){
         //buscamos el mensaje para eliminarlo
         $mensajes = Mensajes::where("id", $id)->first();
         $idUsuario = DB::table('usuarios')->where('id', '=', $mensajes->destino)->first();
 
         if(!empty($mensajes)){  //comprobamos que el mensaje existe
            try{
                $mensajes = Mensajes::where("id", $id)->delete();

                //registramos la operacion en la base de datos
                $logs = DB::table('logs')->insert(array(
                    'Usuario' => $idUsuario->usuario,  //extraer el nombre de usuario para insertarlo
                    'Perfil' => $idUsuario->perfil, //el usuario puede eliminar sus mensajes, comprobar que perfil tiene
                    'Accion' => 'Mensaje eliminado',
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
                "detalles"=>"Se ha borrado su mensaje con éxito"

            );
 
         }else{   //si no se encuentra se lo comunicamos al cliente
 
             $json = array( 
 
                 "status"=>404,
                 "detalles"=>"El mensaje no existe"
             );
 
 
         }
         return json_encode($json, true);  //mandamos la información al cliente
     }

     /**
	 * Método que permite leer un mensaje
	 * cuyo id coincide con el que nos envia el navegador del Cliente
	 * 
	 * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
	public function leido($id){

		if(!empty($id)){
            try{
                $mensaje = DB::table('mensajes')->where("id", $id)->update(array(
                    'leido' => 1,
                    
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

			$json = array(

				"status"=>200,
				"detalles"=>"Mensaje leido"
				
			);
		}else{

			$json = array(

				"status"=>404,
				"detalles"=>"No se ha obtenido los datos"
			
			);
		}

		return json_encode($json, true);   //mandamos la información al cliente
	}

    /**
	 * Método que obtiene de la base de datos el listado de usuarios que no están activos
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function noLeidos($id){
    	$mensajes = DB::table('mensajes')->where('destino', '=', $id)->where('leido', '=', 0)->count();
		if(!empty($mensajes)){

            $json = array(

                "status"=>200,
                "detalles"=>$mensajes
                
            );

        }else{

            $json = array(

                "status"=>200,
                "detalles"=>"No hay ningún usuario"
                
            );

        }
        return json_encode($json, true);

    }
}
