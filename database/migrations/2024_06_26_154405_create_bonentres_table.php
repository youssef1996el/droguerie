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
        Schema::create('bonentres', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bon')->nullable();
            $table->string('date')->nullable();
            $table->string('numero')->nullable();
            $table->string('commercial')->nullable();
            $table->string('mode_paiement')->nullable();
            $table->string('matricule')->nullable();
            $table->string('chauffeur')->nullable();
            $table->string('cin')->nullable();
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
        Schema::dropIfExists('bonentres');
    }
};
