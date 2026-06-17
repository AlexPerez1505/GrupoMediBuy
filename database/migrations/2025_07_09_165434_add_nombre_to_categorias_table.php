<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreToCategoriasTable extends Migration
{
    public function up()
{
    if (!Schema::hasColumn('categorias', 'nombre')) {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('nombre');
        });
    }
}

    public function down()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
}
