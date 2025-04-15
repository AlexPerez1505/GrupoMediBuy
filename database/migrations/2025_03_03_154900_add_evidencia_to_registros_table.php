<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvidenciaToRegistrosTable extends Migration
{
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            // Verificamos si la columna no existe antes de agregarla
            if (!Schema::hasColumn('registros', 'proximo_mantenimiento')) {
                $table->date('proximo_mantenimiento')->nullable();
            }
            
            // Agregar las columnas de evidencia si no existen
            if (!Schema::hasColumn('registros', 'evidencia1')) {
                $table->string('evidencia1')->nullable();
            }
            if (!Schema::hasColumn('registros', 'evidencia2')) {
                $table->string('evidencia2')->nullable();
            }
            if (!Schema::hasColumn('registros', 'evidencia3')) {
                $table->string('evidencia3')->nullable();
            }
            
            // Agregar la columna 'user_name' si no existe
            if (!Schema::hasColumn('registros', 'user_name')) {
                $table->string('user_name'); // Almacena el nombre del usuario
            }
        });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            // Eliminamos las columnas si existieron
            $table->dropColumn(['proximo_mantenimiento', 'evidencia1', 'evidencia2', 'evidencia3', 'user_name']);
        });
    }
}
