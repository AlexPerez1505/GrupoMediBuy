<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvidenciaToRegistrosTable extends Migration
{
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            // Verificar si la columna 'evidencia' existe antes de eliminarla
            if (Schema::hasColumn('registros', 'evidencia')) {
                $table->dropColumn('evidencia');
            }

            // ✅ Verificar antes de agregar cada columna
            if (!Schema::hasColumn('registros', 'evidencia1')) {
                $table->string('evidencia1')->nullable();
            }
            if (!Schema::hasColumn('registros', 'evidencia2')) {
                $table->string('evidencia2')->nullable();
            }
            if (!Schema::hasColumn('registros', 'evidencia3')) {
                $table->string('evidencia3')->nullable();
            }
            if (!Schema::hasColumn('registros', 'user_name')) {
                $table->string('user_name')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->dropColumn(['evidencia1', 'evidencia2', 'evidencia3', 'user_name']);

            if (!Schema::hasColumn('registros', 'evidencia')) {
                $table->text('evidencia')->nullable();
            }
        });
    }
}