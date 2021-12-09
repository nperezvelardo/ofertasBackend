<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Famprof;

class FamiliaProfesionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $fam = new Famprof();
        $fam->Nombre = "Informática";
        $fam->save();

        $fam = new Famprof();
        $fam->Nombre = "Administración de Empresa";
        $fam->save();

        $fam = new Famprof();
        $fam->Nombre = "Comercio";
        $fam->save();
    }
}
