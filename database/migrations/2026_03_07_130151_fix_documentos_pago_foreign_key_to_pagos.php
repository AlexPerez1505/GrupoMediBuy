<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('documentos_pago')) {
            Schema::table('documentos_pago', function (Blueprint $table) {
                // Comentamos el dropForeign por nombre para que SQLite no truene al compilar
                // $table->dropForeign('documentos_pago_pago_id_foreign');
                
                // Opcional si prefieres intentar borrarla usando el método del array compatible:
                try {
                    $table->dropForeign(['pago_id']);
                } catch (\Throwable $e) {}
            });

            Schema::table('documentos_pago', function (Blueprint $table) {
                $table->foreign('pago_id')
                    ->references('id')
                    ->on('pagos')
                    ->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('documentos_pago')) {
            Schema::table('documentos_pago', function (Blueprint $table) {
                // También lo comentamos en el método down
                // $table->dropForeign('documentos_pago_pago_id_foreign');
                
                try {
                    $table->dropForeign(['pago_id']);
                } catch (\Throwable $e) {}
            });

            Schema::table('documentos_pago', function (Blueprint $table) {
                $table->foreign('pago_id')
                    ->references('id')
                    ->on('pagos_financiamiento')
                    ->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};