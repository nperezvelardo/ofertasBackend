<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nif', 9);
            $table->string('nombre', 25);
            $table->string('apellido1', 40);
            $table->string('apellido2', 40);
            $table->string('usuario', 20);
            $table->string('password', 16);
            $table->integer('telefono', 9);
            $table->string('email', 50);
            $table->string('web', 50);
            $table->string('blog', 50);
            $table->string('gitHub', 50);
            $table->boolean('activo');
            $table->integer('promocion', 4);
            $table->string('perfil', 5);
            $table->text('pdf');
            $table->text('foto');
            $table->boolean('verEmpresas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
