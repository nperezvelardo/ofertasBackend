<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuarios;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $usuario = new Usuarios();
        $usuario->nif = '12345678A';
        $usuario->nombre = 'Noelia';
        $usuario->apellido1 = 'Pérez';
        $usuario->apellido2 = 'Velardo';
        $usuario->usuario = 'noe86';
        $usuario->password = '123456';
        $usuario->telefono = 666666666;
        $usuario->email = 'noelia@gmail.com';
        $usuario->web = 'noelia';
        $usuario->blog = 'noelia';
        $usuario->gitHub = 'noelia';
        $usuario->activo = 1;
        $usuario->promocion = 2021;
        $usuario->perfil = "Admin";
        $usuario->save();
    }
}
