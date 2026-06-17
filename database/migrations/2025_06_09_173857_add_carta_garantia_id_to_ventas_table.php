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
    if (!Schema::hasColumn('ventas', 'carta_garantia_id')) {
        Schema::table('ventas', function (Blueprint $table) {
            $table->unsignedBigInteger('carta_garantia_id')->nullable(); // O el tipo de dato que tengas
        });
    }
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            //
        });
    }
};
