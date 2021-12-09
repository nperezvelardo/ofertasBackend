<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 20/08/2021
 */

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\ContactoMailable;
use Illuminate\Support\Facades\Mail;

/**
 * Clase controlador de Usuarios que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la parte Cliente para que pueda acceder a las funcionalidades ofrecidas por la Api
 */

class UsuariosController extends Controller{

	/**
	 * Método que obtiene de la base de datos el listado de usuarios
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function index(){
    	$usuario = DB::table('usuarios')->get();
		if(!empty($usuario)){

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
                
            );

        }else{

            $json = array(

                "status"=>200,
                "detalles"=>"No hay ningún usuario"
                
            );

        }
        return json_encode($json, true);

    }

	/**
	 * Método que obtiene de la base de datos el listado de usuarios que están activos
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function userActivos(){
    	$usuario = DB::table('usuarios')->where('activo', '=', 1)->get();
		if(!empty($usuario)){

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
                
            );

        }else{

            $json = array(

                "status"=>200,
                "detalles"=>"No hay ningún usuario"
                
            );

        }
        return json_encode($json, true);

    }

    /**
	 * Método que obtiene de la base de datos el listado de usuarios que están activos sin el usuario que lo consulta
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function userActivosSin($id){
    
    	$usuario = DB::table('usuarios')->where('activo', '=', 1)->where('id','<>', $id)->get();
		if(!empty($usuario)){

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
                
            );

        }else{

            $json = array(

                "status"=>200,
                "detalles"=>"No hay ningún usuario"
                
            );

        }
        return json_encode($json, true);

    }

    /**
	 * Método que obtiene de la base de datos el total de usuarios que no están activos
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function userNoActivos(){
    	$usuario = DB::table('usuarios')->where('activo', '=', 0)->count();
		if(!empty($usuario)){

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
                
            );

        }else{

            $json = array(

                "status"=>200,
                "detalles"=>"No hay ningún usuario"
                
            );

        }
        return json_encode($json, true);

    }

    /**
	 * Método que obtiene de la base de datos el listado de usuarios que no están activos
     * 
     * @return \Illuminate\Http\Response array con la información que obtenemos de la base de datos
	 */
    public function userActivar(){
    	$usuario = DB::table('usuarios')->where('activo', '=', 0)->get();
		if(!empty($usuario)){

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
                
            );

        }else{

            $json = array(

                "status"=>200,
                "detalles"=>"No hay ningún usuario"
                
            );

        }
        return json_encode($json, true);

    }

    /**
	 * Método que crea un registro con los datos que nos transfiere el cliente y lo almacena en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
    public function store(Request $request){

		$contraseña = bcrypt($request->input("password"));

    	//Recoger datos
    	$datos = array("nif"=>$request->input("nif"),
                        "nombre"=>$request->input("nombre"),
    				   "apellido1"=>$request->input("apellido1"),
                       "apellido2"=>$request->input("apellido2"),
    				   "usuario"=>$request->input("usuario"),
                       "password"=>$contraseña,
                       "telefono"=>$request->input("telefono"),
                       "email"=>$request->input("email"),
                       "web"=>$request->input("web"),
                       "blog"=>$request->input("blog"),
                       "github"=>$request->input("github"),
                       "promocion"=>$request->input("promocion"),
                    );

		$codigos = $request->ciclos;
		$codigosU = array_unique($codigos);

        //comprobamos si ya existe el nombre de usuario
		$usu = DB::table('usuarios')->where('usuario', '=', $datos["usuario"])->first();
		if(empty($usu)){
			//comprobamos si ya existe el nif
			$nif = DB::table('usuarios')->where('nif', '=', $datos["nif"])->first();
			if(empty($nif)){
				//comprobamos si ya existe el email
				$email = DB::table('usuarios')->where('email', '=', $datos["email"])->first();
				if(empty($email)){
					//comprobamos si los datos no están vacios
					if(!empty($datos)){
                        try{
                            $usuario = new Usuarios();
                            $usuario->nif = $datos["nif"];
                            $usuario->nombre = $datos["nombre"];
                            $usuario->apellido1 = $datos["apellido1"];
                            $usuario->apellido2 = $datos["apellido2"];
                            $usuario->usuario = $datos["usuario"];
                            $usuario->password = $datos["password"];
                            $usuario->telefono = $datos["telefono"];
                            $usuario->email = $datos["email"];
                            $usuario->web = $datos["web"];
                            $usuario->blog = $datos["blog"];
                            $usuario->gitHub = $datos["github"];
                            $usuario->activo = 0;
                            $usuario->promocion = $datos["promocion"];
                            $usuario->perfil = "User";
                            $usuario->pdf = '';
                            $usuario->foto = '';
                            $usuario->verEmpresas = $request['empresas'];

                            $usuario->save();

                            foreach($codigosU as $codigo){
                                //registramos los ciclos de la oferta creada
                                $ciclos = DB::table('usuarios_ciclo')->insert(array(
                                    'idU' =>$usuario->id,
                                    'CodigoCiclo' => $codigo,
                                ));
                            }

                            //registramos la operacion en la tabla correspondiente
                            $logs = DB::table('logs')->insert(array(
                                'Usuario' => $datos["usuario"],
                                'Perfil' => 'User',
                                'Accion' => 'Usuario creado',
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
                            "detalle"=>$usuario,
                        );
				
					}else{
			
						$json = array(
			
							"status"=>400,
							"detalle"=>"No se han enviado datos",
						);
			
					}
				}else{
					$json = array(

						"status"=>404,
						"detalle"=>"Email ya registrado",
						
					);
				}
			}else{
				$json = array(

					"status"=>404,
					"detalle"=>"Nif ya registrado",
					
				);
			}

		}else{
			$json = array(

				"status"=>404,
				"detalle"=>"Usuario ya registrado",
				
			);

		}

    	
		return json_encode($json, true);

	}

	/**
 	 * Método que realiza la eliminación de un usuario a través del campo id
	 *
	 * @param  int  $id identificador del recurso a eliminar
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
    public function destroy($id){
		//buscamos el usuario que vamos a eliminar
		$usuario = Usuarios::where("id", $id)->get(); 

		if(!empty($usuario)){ //comprobamos que ese usuario se encuentra en la base de datos
            try{
                $usuario = Usuarios::where("id", $id)->delete(); 
                $ciclos = DB::table('usuarios_ciclo')->where("idU", "=", $id)->delete();
                
                //tenemos que añadir que borre sus mensajes
    
                //registramos la operacion en la base de datos
                $logs = DB::table('logs')->insert(array(
                    'Usuario' => 'noe86',  //extraer el nombre de usuario para insertarlo
                    'Perfil' => 'Admin',
                    'Accion' => 'Usuario eliminado',
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
                "detalles"=>"Se ha borrado el usuario con éxito"

            );

        }else{ //si no se encuentra se lo comunicamos al cliente

            $json = array(

                "status"=>404,
                "detalles"=>"El usuario no existe"
            );


        }
        return json_encode($json, true); //mandamos la información al cliente
	}

	/**
     * Mostramos un recurso especifico. Mediante su identificador buscamos el registro para mostrar todos los datos
     *
     * @param  int  $id identificador del usuario
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
    public function show($id){
        //buscamos el registro
        $usuario = DB::table('usuarios')
                    ->where('id', '=', $id)->first();
        
        if(!empty($usuario)){  //comprobamos que no está vacio

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
                
            );

        }else{

            $json = array(

                "status"=>400,
                "detalles"=>"No hay ningún usuario registrado"
                
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

	/**
	 * Método que permite actualizar los datos del usuario
	 * cuyo id coincide con el que nos envia el navegador del Cliente
	 * 
	 * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
	public function update(Request $request){

    	//Recoger datos
    	$datos = array(
                        "nombre"=>$request->input("nombre"),
    				   "apellido1"=>$request->input("apellido1"),
                       "apellido2"=>$request->input("apellido2"),
    				   "usuario"=>$request->input("usuario"),
                       "telefono"=>$request->input("telefono"),
                       "email"=>$request->input("email"),
                       "web"=>$request->input("web"),
                       "blog"=>$request->input("blog"),
                       "github"=>$request->input("github"),
                       "promocion"=>$request->input("promocion"),
                    );

		$codigos = $request->ciclos;
        $codigosU = array_unique($codigos);
            
        if(!empty($request["id"])){
            try{
                $usuario = DB::table('usuarios')->where("id", $request["id"])->update(array(
                    'nombre' => $datos["nombre"],
                    'apellido1' => $datos["apellido1"],
                    'apellido2' => $datos["apellido2"],
                    'usuario' => $datos["usuario"],
                    'telefono' => $datos["telefono"],
                    'web' => $datos["web"],
                    'blog' => $datos["blog"],
                    'gitHub' => $datos["github"],
                    'promocion' => $datos["promocion"]
                ));
    
                foreach($codigosU as $codigo){
                    //registramos los ciclos de la oferta actualizada
                    $ciclos = DB::table('usuarios_ciclo')->insert(array(
                        'idU' =>$request["id"],
                        'CodigoCiclo' => $codigo,
                    ));
                }
    
                //registramos la operacion en la base de datos
                $logs = DB::table('logs')->insert(array(
                    'Usuario' => $datos["usuario"],  
                    'Perfil' => $request["perfil"],  
                    'Accion' => 'Usuario actualizado',
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
                "detalles"=>"Usuario actualizado"

            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido los datos del usuario"
            
            );
        }		
	
		return json_encode($json, true);   //mandamos la información al cliente
	}

	/**
	 * Método que permite activar el usuario
	 * cuyo id coincide con el que nos envia el navegador del Cliente
	 * 
	 * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
	public function activar($id){

		if(!empty($id)){
            try{
                $usuario = DB::table('usuarios')->where("id", $id)->update(array(
                    'activo' => 1,
                    
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

			$json = array(

				"status"=>200,
				"detalles"=>"Usuario activado"
				
			);
		}else{

			$json = array(

				"status"=>404,
				"detalles"=>"No se ha obtenido los datos del usuario"
			
			);
		}

		return json_encode($json, true);   //mandamos la información al cliente
	}
    
    /**
	 * Método que permite desactivar el usuario
	 * cuyo id coincide con el que nos envia el navegador del Cliente
	 * 
	 * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
	public function desactivar($id){

		if(!empty($id)){
            try{
                $usuario = DB::table('usuarios')->where("id", $id)->update(array(
                    'activo' => 0,
                    
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

			$json = array(

				"status"=>200,
				"detalles"=>"Usuario desactivado"
				
			);
		}else{

			$json = array(

				"status"=>404,
				"detalles"=>"No se ha obtenido los datos del usuario"
			
			);
		}

		return json_encode($json, true);   //mandamos la información al cliente
	}

	/**
     * Mostramos un recurso especifico. Mediante su email buscamos el registro para mostrar todos los datos
     *
     * @param  int  $email email del usuario
     * @return \Illuminate\Http\Response  array con la información para el cliente
     */
    public function email($email){
        //buscamos el registro
        $usuario = DB::table('usuarios')
                    ->where('email', '=', $email)->first();
        
        if(!empty($usuario)){  //comprobamos que no está vacio

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
                
            );

        }else{

            $json = array(

                "status"=>400,
                "detalles"=>"No hay ningún usuario registrado"
                
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

	/**
	 * Función para exportar a excel la lista de usuarios
	 */
	public function exportExcel(){
        return Excel::download(new UsersExport, 'listaUsuarios.xlsx');
    }

	/**
	 * Función para importar usuarios desde excel
	 */
	public function importExcel(Request $request){

        $json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR
    
        $params = json_decode($json); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE
        
        $nombre = $params->nombre;
        $nombreArchivo = $params->nombreArchivo;
        $archivo = $params->base64textString;
        $archivo = base64_decode($archivo);
        $nombreA = time().$nombreArchivo;
        $filePath = $_SERVER['DOCUMENT_ROOT']."/storage/Importar/".$nombreA;
        if (!is_dir($_SERVER['DOCUMENT_ROOT']."/storage/Importar/"))
        {
            mkdir($_SERVER['DOCUMENT_ROOT']."/storage/Importar/", 0777, true);
        }
        file_put_contents($filePath, $archivo);
        
        Excel::import(new UsersImport, $filePath);

		$json = array(

			"status"=>200,
			"detalles"=>'Importación de usuarios completada'
			
		);

        return json_encode($json, true);  //mandamos la información al cliente
    }
	/**
	 * Función para enviar un email cuando se active el usuario
	 */
	public function enviarEmail($email){
        $details = [
                    'tipo' => 1,
        ];		

		Mail::to($email)->send(new \App\Mail\ContactoMailable($details));
		$json = array(

			"status"=>200,
			"detalles"=>'Correo enviado correctamente'
			
		);
		return json_encode($json, true);  //mandamos la información al cliente
	}
    
    /**
	 * Función para enviar un email cuando se registre el usuario
	 */
	public function enviarEmailRegistro(){
        //buscamos el último usuario que se ha registrado
        $user = Usuarios::latest('id')->first();

        $details = [
                    'tipo' => 2,
        ];		

		Mail::to($user->email)->send(new \App\Mail\ContactoMailable($details));
		$json = array(

			"status"=>200,
			"detalles"=>'Correo enviado correctamente'
			
		);
		return json_encode($json, true);  //mandamos la información al cliente
	}
}
