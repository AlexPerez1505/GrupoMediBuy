<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->unsignedBigInteger('carta_garantia_id')->nullable()->after('detalle_financiamiento');
            $table->foreign('carta_garantia_id')
                ->references('id')
                ->on('carta_garantias')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['carta_garantia_id']);
            $table->dropColumn('carta_garantia_id');
        });
    }
};

