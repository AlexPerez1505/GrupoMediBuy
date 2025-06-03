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
      Schema::table('pagos', function (Blueprint $table) {
        $table->unsignedBigInteger('venta_id')->nullable()->after('id');

        $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('pagos', function (Blueprint $table) {
        $table->dropForeign(['venta_id']);
        $table->dropColumn('venta_id');
    });
    }
};
