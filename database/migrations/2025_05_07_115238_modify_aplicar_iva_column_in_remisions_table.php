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
        Schema::table('remisions', function (Blueprint $table) {
            // Cambiar tipo de columna 'aplicar_iva'
            $table->boolean('aplicar_iva')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remisions', function (Blueprint $table) {
            //
        });
    }
};
