<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('documentos_pago', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pago_id')->constrained('pagos')->onDelete('cascade');
        $table->string('nombre_original');
        $table->string('ruta_archivo');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_pago');
    }
};
