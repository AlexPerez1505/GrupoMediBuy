<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->text('comentario')->nullable()->after('estatus');
        });
    }
    
    public function down()
    {
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->dropColumn('comentario');
        });
    }
    
};
