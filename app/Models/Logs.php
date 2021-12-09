<?php

/**
 * Autor: Noelia Pérez Velardo
 * Fecha: 17/08/2021
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;
    //determinamos la tabla dónde está relacionada el modelo
    protected $table = 'logs';
}
