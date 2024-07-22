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
        Schema::create('soldecaisse', function (Blueprint $table) {
            $table->id();
            $table->decimal('total',10,2)->nullable();
            $table->foreignId('idcompany')->references('id')->on('company')->onDelete('cascade');
            $table->foreignId('iduser')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soldecaisse');
    }
};
