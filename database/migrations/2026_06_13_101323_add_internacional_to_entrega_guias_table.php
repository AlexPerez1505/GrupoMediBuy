<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up() {
    Schema::table('entrega_guias', function (Blueprint $table) {
        $table->boolean('internacional')->default(false)->after('destinatario');
    });
}

public function down() {
    Schema::table('entrega_guias', function (Blueprint $table) {
        $table->dropColumn('internacional');
    });
}
};
