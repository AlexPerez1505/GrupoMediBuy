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
        Schema::create('cuentas', function (Blueprint $table) {
    $table->id();
    $table->decimal('casetas', 8, 2);
    $table->decimal('gasolina', 8, 2);
    $table->decimal('viaticos', 8, 2);
    $table->decimal('adicional', 8, 2);
    $table->string('descripcion');
    $table->decimal('total', 8, 2);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
