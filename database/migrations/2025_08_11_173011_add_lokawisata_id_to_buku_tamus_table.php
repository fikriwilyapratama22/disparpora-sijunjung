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
    Schema::table('buku_tamus', function (Blueprint $table) {
        $table->unsignedBigInteger('lokawisata_id')->after('id')->nullable();
        $table->foreign('lokawisata_id')
              ->references('id')
              ->on('info_tempats')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('buku_tamus', function (Blueprint $table) {
        $table->dropForeign(['lokawisata_id']);
        $table->dropColumn('lokawisata_id');
    });
}
};
