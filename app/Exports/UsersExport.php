<?php

namespace App\Exports;

use App\Usuarios;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'id',
            'nif',
            'nombre',
            'apellido1', 
            'apellido2',
            'usuario',
            'password',
            'telefono',
            'email',
            'web',
            'blog',
            'gitHub',
            'activo',
            'promocion',
            'perfil',
            'creado',
            'actualizado',
            'pdf',
            'foto',
            'verEmpresas',
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('usuarios')->get();
    }
}
