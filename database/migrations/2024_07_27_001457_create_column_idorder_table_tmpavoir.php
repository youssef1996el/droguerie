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
        Schema::table('tmpavoir', function (Blueprint $table) {
            $table->unsignedBigInteger('idorder')->nullable();
            $table->foreign('idorder')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tmpavoir', function (Blueprint $table) {
            $table->dropForeign(['idorder']);
            $table->dropColumn('idorder');
        });
    }
};
