<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 21/08/2021
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ofertas_ciclo extends Model
{
    use HasFactory;
    //determinamos la tabla dónde está relacionada el modelo
    protected $table = 'ofertas_ciclo';
}
