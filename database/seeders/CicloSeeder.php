<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ciclo;

class CicloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $ciclo = new Ciclo();
        $ciclo->Nombre = 'Daw';
        $ciclo->CodigoFam = 1;
        $ciclo->save();

        $ciclo = new Ciclo();
        $ciclo->Nombre = 'Asir';
        $ciclo->CodigoFam = 1;
        $ciclo->save();

        $ciclo = new Ciclo();
        $ciclo->Nombre = 'SMR';
        $ciclo->CodigoFam = 1;
        $ciclo->save();

        $ciclo = new Ciclo();
        $ciclo->Nombre = 'Gestión Administrativa';
        $ciclo->CodigoFam = 2;
        $ciclo->save();

        $ciclo = new Ciclo();
        $ciclo->Nombre = 'Administración y Finanzas';
        $ciclo->CodigoFam = 2;
        $ciclo->save();

        $ciclo = new Ciclo();
        $ciclo->Nombre = 'Actividades Comerciales';
        $ciclo->CodigoFam = 3;
        $ciclo->save();

        $ciclo = new Ciclo();
        $ciclo->Nombre = 'Gestión de Ventas';
        $ciclo->CodigoFam = 3;
        $ciclo->save();
    }
}
