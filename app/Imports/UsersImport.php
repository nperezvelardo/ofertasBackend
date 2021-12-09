<?php

namespace App\Imports;

use App\Models\Usuarios;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Usuarios([
            'nif' => $row[1],
            'nombre' => $row[2], 
            'apellido1' => $row[3], 
            'apellido2' =>  $row[4],
            'usuario' =>  $row[5],
            'password' =>  bcrypt($row[6]),
            'telefono' =>  $row[7],
            'web' =>  $row[8],
            'blog' =>  $row[9],
            'gitHub' =>  $row[10],
            'promocion' =>  $row[11],
            'perfil' =>  $row[12],
            'pdf' => $row[13],
            'foto' => $row[14],
        ]);
    }
}
