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
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('name_product')->nullable();
            $table->string('qte')->nullabale();
            $table->string('convert')->nullabel();
            $table->foreignId('idcategory')->references('id')->on('categorys')->onDelete('cascade');
            $table->foreignId('idstock')->references('id')->on('stock')->onDelete('cascade');
            $table->foreignId('iduser')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('idcompany')->references('id')->on('company')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};
