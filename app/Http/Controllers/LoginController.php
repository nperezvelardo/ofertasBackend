<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 17/08/2021
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ofertas;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

/**
 * Clase controlador de Login que será la encargada de loguear a los usuarios
 * Comprobará si los datos introducidos son correctos para que el usuario pueda accder
 * También restablecera la contraseña en caso de que el usuario la olvidase
 */
class LoginController extends Controller{

    /**
     * Método que comprueba si el email y la contaseña coinciden con los usuarios de la base de datos
     * Comprueba si están activados para poder acceder
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request){
        $credentials = $this->validate(request(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $Password=$request['password'];
        $usuario = DB::table('usuarios')->where('email', '=', $credentials['email'])->first();

        //comprobamos que existe el usuario
        if(!empty($usuario)){
            $hashedPass=$usuario->password;
            if (Hash::check($Password, $hashedPass)) {
                
                //comprobamos que ese usuario esta activado
                if($usuario->activo == 1){
                    $json = array(

                        "status"=>200,
                        "detalles"=>$usuario
                        
                    );

                    //registramos la operacion en la tabla correspondiente
                    try{
                        $logs = DB::table('logs')->insert(array(
                            'Usuario' => $usuario->usuario,
                            'Perfil' => $usuario->perfil,
                            'Accion' => 'Login',
                            'fecha' => NOW(),
                        ));
            
                    } catch (\Illuminate\Database\QueryException $exception) {
                        Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                        DB::rollBack();
                        return $exception;
                    }
    
                    DB::commit();
                    

                }else{
                    $json = array(

                        "status"=>401,
                        "detalles"=>"El usuario no está activado"
                        
                    );
                }
            }else{
                $json = array(

                    "status"=>400,
                    "detalles"=>"La contraseña no es correcta"
                    
                );
            }

            

        }else{

            $json = array(

                "status"=>400,
                "detalles"=>"El usuario no coincide"
                
            );

        }
        return json_encode($json, true);
    }

    /**
     * Método que comprueba si el email coinciden con los usuarios de la base de datos
     * Comprueba si están activados para poder acceder
     *
     * @param $email
     * @return void
     */
    public function loginGoogle($email){

        $usuario = DB::table('usuarios')->where('email', '=', $email)->first();

        //comprobamos que existe el usuario
        if(!empty($usuario)){
            //comprobamos que ese usuario esta activado
            if($usuario->activo == 1){
                $json = array(

                    "status"=>200,
                    "detalles"=>$usuario
                    
                );

                //registramos la operacion en la tabla correspondiente
                try{
                    $logs = DB::table('logs')->insert(array(
                        'Usuario' => $usuario->usuario,
                        'Perfil' => $usuario->perfil,
                        'Accion' => 'Login',
                        'fecha' => NOW(),
                    ));
        
                } catch (\Illuminate\Database\QueryException $exception) {
                    Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                    DB::rollBack();
                    return $exception;
                }

                DB::commit();

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

    /**
     * Método que comprueba si el email existe en la base de datos para poder restablecer la contraseña
     *
     * @param Request $request
     * @return void
     */
    public function restablecer(Request $request){

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

    /**
	 * Método que crea un registro con los datos que nos transfiere el cliente y lo almacena en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
    public function createMensaje(Request $request){
         
        //Recoger datos
        $datos = $this->validate(request(), [
            'titulo' => 'required|string',
            'contenido' => 'required|string',
        ]);

        $destino = $request->destino;
        $destU = array_unique($destino);

        $usuario = DB::table('usuarios')
                    ->where('id', '=', $request["usuario"])->first();
 
         //comprobamos que existen datos
         if(!empty($datos)){
            foreach($destU as $dest){
                try{
                    $mensaje = DB::table('mensajes')->insert(array(
                        'titulo' => $datos["titulo"],
                        'contenido' => strip_tags($datos["contenido"]),
                        'usuario' => $request["usuario"],
                        'fecha' => NOW(),
                        'destino' => $dest,
                        'leido' => 0,
                        'updated_at' => NOW(),
                        'created_at'=> NOW(),
                        ));
    
                    //registramos la operacion en la tabla base de datos
                    $logs = DB::table('logs')->insert(array(
                        'Usuario' => $usuario->usuario,  //nombre de usuario para insertarlo
                        'Perfil' => $usuario->perfil, //el usuario puede enviar mensajes, comprobar que perfil tiene
                        'Accion' => 'Mensaje enviado',
                        'fecha' => NOW(),
                    ));
        
                } catch (\Illuminate\Database\QueryException $exception) {
                    Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                    DB::rollBack();
                    return $exception;
                }

                DB::commit();  
            }


                $json = array(

                    "status"=>200,
                    "detalles"=>$mensaje
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
	 * Método que crea un registro con los datos que nos transfiere el cliente y lo almacena en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request objeto que permite el acceso a toda la información 
	 * que se pasa desde el navegador del Cliente
     * @return \Illuminate\Http\Response array con la información de si se ha realizado o no el proceso
	 */
    public function createOferta(Request $request){

        //recoger datos
        $datos = $this->validate(request(), [
            'empresa' => 'required|string',
            'informacion' => 'required|string',
        ]);

        $codigos = $request->ciclos;
        $codigosU = array_unique($codigos);

        //comprobamos que existen datos
        if(!empty($datos)){
            try{
                $ofertas = new Ofertas();
                $ofertas->empresa = $request["empresa"];
                $ofertas->fecha = NOW();
                $ofertas->informacion = strip_tags($request["informacion"]);
                $ofertas->pdf = "oferta.pdf";  //cambiar por archivo seleccionado

                $ofertas->save();

                foreach($codigosU as $codigo){
                    //registramos los ciclos de la oferta creada
                    $ciclos = DB::table('ofertas_ciclo')->insert(array(
                        'idO' =>$ofertas->id,
                        'CodigoCiclo' => $codigo,
                    ));
                }

                //registramos la operacion en la tabla correspondiente
                $logs = DB::table('logs')->insert(array(
                    'Usuario' => $request->input("usuario"),  
                    'Perfil' => 'Admin',
                    'Accion' => 'Oferta creada',
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
     * Método para crear el archivo pdf de la oferta
     */
    public function createArchivo(){
        $json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR
    
        $params = json_decode($json); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE
        
        $nombre = $params->nombre;
        $nombreArchivo = $params->nombreArchivo;
        $archivo = $params->base64textString;
        $archivo = base64_decode($archivo);
        $nombreA = time().$nombreArchivo;
        $filePath = $_SERVER['DOCUMENT_ROOT']."/storage/OfertasPdf/".$nombreA;
        if (!is_dir($_SERVER['DOCUMENT_ROOT']."/storage/OfertasPdf/"))
        {
            mkdir($_SERVER['DOCUMENT_ROOT']."/storage/OfertasPdf/", 0777, true);
        }
        file_put_contents($filePath, $archivo);

        $data = Ofertas::latest('id')->first();
        if(!empty($data)){
            try{
                $oferta = DB::table('ofertas')->where("id", $data->id)->update(array(
                    'pdf' => 'http://ofertasapp.es/storage/OfertasPdf/'.$nombreA
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalles"=>$oferta
            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido la oferta"
            
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

    /**
     * Método para crear el archivo pdf del curriculum del usuario
     */
    public function createCurriculum(){
        $json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR
    
        $params = json_decode($json); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE
        
        $nombre = $params->nombre;
        $nombreArchivo = $params->nombreArchivo;
        $archivo = $params->base64textString;
        $archivo = base64_decode($archivo);
        $nombreA = time().$nombreArchivo;
        $filePath = $_SERVER['DOCUMENT_ROOT']."/storage/Curriculum/".$nombreA;
        if (!is_dir($_SERVER['DOCUMENT_ROOT']."/storage/Curriculum/"))
        {
            mkdir($_SERVER['DOCUMENT_ROOT']."/storage/Curriculum/", 0777, true);
        }
        file_put_contents($filePath, $archivo);

        $data = Usuarios::latest('id')->first();
        if(!empty($data)){
            try{
                $usuario = DB::table('usuarios')->where("id", $data->id)->update(array(
                    'pdf' => 'http://ofertasapp.es/storage/Curriculum/'.$nombreA
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido el usuario"
            
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }
    
    /**
     * Método para crear la imagen del usuario
     */
    public function createImagen(){
        $json = file_get_contents('php://input'); //recibimos el json de angular
        $parametros = json_decode($json);// decodifica el json y lo guarda en paramentros

        $nombre = $parametros->nombre;
        $nombreArchivo = $parametros->nombreArchivo;
        $archivo = $parametros->base64textString;
        $archivo = base64_decode($archivo);
        $nombreA = time().$nombreArchivo;

        $filePath = $_SERVER['DOCUMENT_ROOT']."/storage/Fotos/".$nombreA;
        if (!is_dir($_SERVER['DOCUMENT_ROOT']."/storage/Fotos/"))
        {
            mkdir($_SERVER['DOCUMENT_ROOT']."/storage/Fotos/", 0777, true);
        }
        file_put_contents($filePath, $archivo);

        $data = Usuarios::latest('id')->first();
        if(!empty($data)){
            try{
                $usuario = DB::table('usuarios')->where("id", $data->id)->update(array(
                    'foto' => 'http://ofertasapp.es/storage/Fotos/'.$nombreA
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido el usuario"
            
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente

    }

     /**
     * Método para actualizar el archivo pdf de la oferta
     */
    public function updateArchivo($id){
        $json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR
    
        $params = json_decode($json); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE
        
        $nombre = $params->nombre;
        $nombreArchivo = $params->nombreArchivo;
        $archivo = $params->base64textString;
        $archivo = base64_decode($archivo);
        $nombreA = time().$nombreArchivo;
        $filePath = $_SERVER['DOCUMENT_ROOT']."/storage/OfertasPdf/".$nombreA;
        if (!is_dir($_SERVER['DOCUMENT_ROOT']."/storage/OfertasPdf/"))
        {
            mkdir($_SERVER['DOCUMENT_ROOT']."/storage/OfertasPdf/", 0777, true);
        }
        file_put_contents($filePath, $archivo);

        if(!empty($id)){
            try{
                $oferta = DB::table('ofertas')->where("id", $id)->update(array(
                    'pdf' => 'http://ofertasapp.es/storage/OfertasPdf/'.$nombreA
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalles"=>$oferta
            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido la oferta"
            
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }

    /**
     * Método para actualizar el archivo pdf del curriculum del usuario
     */
    public function updateCurriculum($id){
        $json = file_get_contents('php://input'); // RECIBE EL JSON DE ANGULAR
    
        $params = json_decode($json); // DECODIFICA EL JSON Y LO GUARADA EN LA VARIABLE
        
        $nombre = $params->nombre;
        $nombreArchivo = $params->nombreArchivo;
        $archivo = $params->base64textString;
        $archivo = base64_decode($archivo);
        $nombreA = time().$nombreArchivo;
        $filePath = $_SERVER['DOCUMENT_ROOT']."/storage/Curriculum/".$nombreA;
        if (!is_dir($_SERVER['DOCUMENT_ROOT']."/storage/Curriculum/"))
        {
            mkdir($_SERVER['DOCUMENT_ROOT']."/storage/Curriculum/", 0777, true);
        }
        file_put_contents($filePath, $archivo);

        if(!empty($id)){
            try{
                $usuario = DB::table('usuarios')->where("id", $id)->update(array(
                    'pdf' => 'http://ofertasapp.es/storage/Curriculum/'.$nombreA
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido el usuario"
            
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente
    }
    
    /**
     * Método para actualizar la imagen del usuario
     */
    public function updateImagen($id){
        $json = file_get_contents('php://input'); //recibimos el json de angular
        $parametros = json_decode($json);// decodifica el json y lo guarda en paramentros

        $nombre = $parametros->nombre;
        $nombreArchivo = $parametros->nombreArchivo;
        $archivo = $parametros->base64textString;
        $archivo = base64_decode($archivo);
        $nombreA = time().$nombreArchivo;

        $filePath = $_SERVER['DOCUMENT_ROOT']."/storage/Fotos/".$nombreA;
        if (!is_dir($_SERVER['DOCUMENT_ROOT']."/storage/Fotos/"))
        {
            mkdir($_SERVER['DOCUMENT_ROOT']."/storage/Fotos/", 0777, true);
        }
        file_put_contents($filePath, $archivo);

        if(!empty($id)){
            try{
                $usuario = DB::table('usuarios')->where("id", $id)->update(array(
                    'foto' => 'http://ofertasapp.es/storage/Fotos/'.$nombreA
                ));
    
            } catch (\Illuminate\Database\QueryException $exception) {
                Session::flash('error', "Excepción en la inserción de subidas: " . $exception);
                DB::rollBack();
                return $exception;
            }

            DB::commit();

            $json = array(

                "status"=>200,
                "detalles"=>$usuario
            );
        }else{

            $json = array(

                "status"=>404,
                "detalles"=>"No se ha obtenido el usuario"
            
            );

        }
        return json_encode($json, true);  //mandamos la información al cliente

    }
    
    /**
     * Método que registra la operación de logout en la base de datos
     *
     * @param $usuario
     * @return void
     */
    public function logOut($usuario){
        //buscamos el perfil del usuario mediante su nombre de usuario
        $usu = DB::table('usuarios')
                    ->where('usuario', '=', $usuario)->first();
        //registramos la operacion en la tabla correspondiente
        try{
            $logs = DB::table('logs')->insert(array(
                'Usuario' => $usuario,
                'Perfil' => $usu->perfil,
                'Accion' => 'Logout',
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
            "detalles"=>'Operacion registrada correctamente',
        );

        return json_encode($json, true);
    }

}
