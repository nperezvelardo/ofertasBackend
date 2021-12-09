<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 17/08/2021
 */

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OfertasExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ofertas;
use App\Mail\ContactoMailable;
use Illuminate\Support\Facades\Mail;

/**
 * Clase controlador de Ofertas que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class OfertasController extends Controller{

    /**
	 * Método que obtiene de la base de datos el listado de ofertas
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function index(){

       $ofertas = DB::table('ofertas')->get();
       if(!empty($ofertas)){

        $json = array(

            "status"=>200,
            "total_registros"=>count($ofertas),
            "detalles"=>$ofertas
            
        );

        }else{

            $json = array(

                "status"=>400,
                "total_registros"=>0,
                "detalles"=>"No hay ninguna oferta registrada"
                
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

        //Buscamos el usuario que registra la oferta a través de los datos enviados
        //$usuario = DB::table('usuarios')->where('id', $request->input("idU"))->first();

        //Recoger datos
        $datos = array( "empresa"=>$request->input("empresa"),
                "informacion"=>$request->input("informacion"),
        );

        //falta recoger los ciclos de la oferta e insertarlos en la tabla ofertas_ciclo

        //comprobamos que existen datos
        if(!empty($datos)){
            $ofertas = new Ofertas();
            $ofertas->empresa = $datos["empresa"];
            $ofertas->fecha = NOW();
            $ofertas->informacion = $datos["informacion"];
            $ofertas->pdf = "oferta.pdf";  //cambiar por archivo seleccionado

            $ofertas->save();

            //registramos la operacion en la tabla correspondiente
            $logs = DB::table('logs')->insert(array(
                'Usuario' => 'nombre usuario',  
                'Perfil' => 'Admin',
                'Accion' => 'Oferta creada',
                'fecha' => NOW(),
            ));


            $json = array(

                "status"=>200,
                "detalles"=>$ofertas
            );

            return json_encode($json, true);

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
     * @param  int  $id identificador de la oferta
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
    public function show($id){
        //buscamos el registro
        $ofertas = DB::table('ofertas')
                    ->where('id', '=', $id)->first();
        
        if(!empty($ofertas)){

            $json = array(

                "status"=>200,
                "detalles"=>$ofertas
                
            );

        }else{

            $json = array(

                "status"=>400,
                "detalles"=>"No hay ninguna oferta registrada"
                
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

    /**
	 * Método que permite actualizar los datos de la oferta
	 * cuyo id coincide con el que nos envia el navegador del Cliente
	 * 
	 * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
    public function update(Request $request){

        //recogemos el valor del identificador
        $id = $request->input("id");

        //recoger datos
        $datos = $this->validate(request(), [
            'empresa' => 'required|string',
            'informacion' => 'required|string',
        ]);

        $codigos = $request->ciclos;
        $codigosU = array_unique($codigos);

        if(!empty($id)){
            try{
                $oferta = DB::table('ofertas')->where("id", $id)->update(array(
                    'empresa' => $datos["empresa"],
                    'informacion' => strip_tags($datos["informacion"]),
                ));
    
                foreach($codigosU as $codigo){ 
                    //registramos los ciclos de la oferta actualizada
                    $ciclos = DB::table('ofertas_ciclo')->insert(array(
                        'idO' =>$id,
                        'CodigoCiclo' => $codigo,
                    ));
                }
    
                //registramos la operacion en la base de datos
                $logs = DB::table('logs')->insert(array(
                    'Usuario' => $request->input("usuario"),  //extraer el nombre de usuario para insertarlo
                    'Perfil' => 'Admin',
                    'Accion' => 'Oferta actualizada',
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
                "detalles"=>"Oferta actualizada"

            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido los datos de la oferta"
            
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

    /**
 	 * Método que realiza la eliminación de una oferta a través del campo id
	 *
	 * @param  int  $id identificador del recurso a eliminar
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
    public function destroy($id){
        //buscamos la oferta a eliminar
        $oferta = Ofertas::where("id", $id)->get();

        if(!empty($oferta)){  //comprobamos que esa oferta existe
            try{
                $oferta = Ofertas::where("id", $id)->delete();
                $ciclos = DB::table('ofertas_ciclo')->where("idO", "=", $id)->delete();

                //registramos la operacion en la base de datos
                $logs = DB::table('logs')->insert(array(
                    'Usuario' => 'noe86',  
                    'Perfil' => 'Admin',
                    'Accion' => 'Oferta Eliminada',
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
                "detalles"=>"Se ha borrado su oferta con éxito"

            );

        }else{  //si no se encuentra se lo comunicamos al cliente

            $json = array(

                "status"=>404,
                "detalles"=>"La oferta no existe"
            );


        }
        return json_encode($json, true); //mandamos la información al cliente
    }

    /**
    * Función para exportar a excel las ofertas disponibles en la base de datos
    */
    public function exportExcel(){
        return Excel::download(new OfertasExport, 'listaOfertas.xlsx');
    }
    
    /**
	 * Función para enviar un email al usuario deseado
	 */
	public function enviarEmailUsuarios(Request $request){
        $details = [
                    'contenido' => strip_tags($request['contenido']),
                    'asunto' => $request['asunto'],
                    'remitente' => $request['remitente'],
                    'tipo' => 3,
        ];		

		Mail::to($request['email'])->send(new \App\Mail\ContactoMailable($details));
		$json = array(

			"status"=>200,
			"detalles"=>'Correo enviado correctamente'
			
		);
		return json_encode($json, true);  //mandamos la información al cliente
	}
}
