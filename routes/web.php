<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 17/08/2021
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfertasController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\MensajesController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\FamprofController;
use App\Http\Controllers\CicloController;
use App\Http\Controllers\Usuarios_cicloController;
use App\Http\Controllers\Ofertas_cicloController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Creamos las rutas de la apiRest para usuarios, ofertas y mensajes
Route::apiResources([
    'ofertas' => OfertasController::class,
    'usuarios' => UsuariosController::class,
    'mensajes' => MensajesController::class,
]);

Route::get('/activar/{id}', [UsuariosController::class, 'activar'])->name('usuarios.activar');
Route::get('/desactivar/{id}', [UsuariosController::class, 'desactivar'])->name('usuarios.desactivar');
Route::get('/email/{email}', [UsuariosController::class, 'email'])->name('usuarios.email');
Route::get('/descargar/{id}', [UsuariosController::class, 'descargar'])->name('usuarios.descargar');
Route::get('/userActivos', [UsuariosController::class, 'userActivos'])->name('usuarios.userActivos');
Route::get('/userActivosSin/{id}', [UsuariosController::class, 'userActivosSin'])->name('usuarios.userActivosSin');
Route::get('/userNoActivos', [UsuariosController::class, 'userNoActivos'])->name('usuarios.userNoActivos');
Route::get('/userActivar', [UsuariosController::class, 'userActivar'])->name('usuarios.userActivar');

Route::get('/descargarO/{id}', [OfertasController::class, 'descargarO'])->name('ofertas.descargarO');

Route::get('leido/{id}', [MensajesController::class, 'leido'])->name('mensajes.leido');
Route::get('noLeidos/{id}', [MensajesController::class, 'noLeidos'])->name('mensajes.noLeidos');

//Las rutas de las operaciones registradas
Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');
Route::post('/logs', [LogsController::class, 'store'])->name('logs.store');
Route::get('/logsE', [LogsController::class, 'destroy'])->name('logs.destroy');
Route::get('/logsDe/{id}', [LogsController::class, 'destroyLog'])->name('logs.destroyLog');

//Ruta para la familias profesionales
Route::get('/fam', [FamprofController::class, 'index'])->name('famprof.index');

//Ruta para los ciclos formativos
Route::get('/ciclo', [CicloController::class, 'index'])->name('ciclo.index');
Route::get('/ciclo/{codigo}', [CicloController::class, 'showNombre'])->name('ciclo.showNombre');
Route::get('/cicloFam/{id}', [CicloController::class, 'show'])->name('ciclo.show');

//Ruta para los ciclos formativos de los usuario
Route::get('/usuCiclo/{id}', [Usuarios_cicloController::class, 'show'])->name('usuarios_ciclo.show');
Route::get('/cicloUsu/{id}', [Usuarios_cicloController::class, 'showU'])->name('usuarios_ciclo.showU');
Route::post('/usuCiclo', [Usuarios_cicloController::class, 'store'])->name('usuarios_ciclo.store');
Route::delete('/usuCiclo/{idU}{Codigo}', [Usuarios_cicloController::class, 'destroy'])->name('usuarios_ciclo.destroy');

//Ruta para los ciclos formativos de las ofertas
Route::get('/ofeCiclo/{id}', [Ofertas_cicloController::class, 'show'])->name('ofertas_ciclo.show');
Route::get('/cicloOfe/{id}', [Ofertas_cicloController::class, 'showOferta'])->name('ofertas_ciclo.showOferta');
Route::post('/ofeCiclo', [Ofertas_cicloController::class, 'store'])->name('ofertas_ciclo.store');
Route::delete('/ofeCiclo/{idO}{Codigo}', [Ofertas_cicloController::class, 'destroy'])->name('ofertas_ciclo.destroy');

Route::post('/login', [LoginController::class, 'login']);
Route::get('/loginGoogle/{email}', [LoginController::class, 'loginGoogle']);
Route::post('/resCon', [CicloController::class, 'resCon'])->name('ciclo.resCon');
Route::post('/createMensaje', [LoginController::class, 'createMensaje']);
Route::post('/createOferta', [LoginController::class, 'createOferta']);
Route::post('/createArchivo', [LoginController::class, 'createArchivo']);
Route::post('/createCurriculum', [LoginController::class, 'createCurriculum']);
Route::post('/createImagen', [LoginController::class, 'createImagen']);
Route::post('/updateArchivo/{id}', [LoginController::class, 'updateArchivo']);
Route::post('/updateCurriculum/{id}', [LoginController::class, 'updateCurriculum']);
Route::post('/updateImagen/{id}', [LoginController::class, 'updateImagen']);
Route::get('/logOut/{usuario}', [LoginController::class, 'logOut']);

Route::get('/excelU', [UsuariosController::class, 'exportExcel'])->name('usuarios.exportExcel');
Route::post('/importExcel', [UsuariosController::class, 'importExcel'])->name('usuarios.importExcel');
Route::get('/excelO', [OfertasController::class, 'exportExcel'])->name('ofertas.exportExcel');

//ruta para enviar email de activacion de usuario
Route::get('/enviarEmail/{email}', [UsuariosController::class, 'enviarEmail'])->name('usuarios.enviarEmail');

//ruta para enviar email de registro de usuario
Route::get('/enviarEmailRegistro', [UsuariosController::class, 'enviarEmailRegistro'])->name('usuarios.enviarEmailRegistro');

//ruta para enviar email entre usuarios
Route::post('/enviarEmailUsuarios', [OfertasController::class, 'enviarEmailUsuarios'])->name('usuarios.enviarEmailUsuarios');

//ruta para enviar sms
Route::get('/sms', [NotificationController::class, 'sendSmsNotificaition']);

Route::get('/', function () {
    return view('welcome');
});
