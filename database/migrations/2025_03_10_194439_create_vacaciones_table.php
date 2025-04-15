<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con la tabla users
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('tipo_permiso', ['Normal', 'Especial'])->nullable();
            $table->text('justificacion')->nullable();
            $table->enum('estatus', ['Pendiente', 'Aprobada', 'Rechazada'])->default('Pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vacaciones');
    }
};
