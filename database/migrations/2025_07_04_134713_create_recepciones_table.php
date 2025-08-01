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
       Schema::create('recepciones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
    $table->date('fecha');
    $table->string('recibido_por');
    $table->text('observaciones')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepciones');
    }
};
