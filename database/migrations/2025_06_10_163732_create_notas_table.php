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
       Schema::create('notas', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('cliente_id');
    $table->text('contenido');
    $table->timestamp('fecha')->nullable(); // opcional
    $table->timestamps();

    $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
