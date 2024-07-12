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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->nullable();
            $table->string('datecheque')->nullable();
            $table->string('datepromise')->nullable();
            $table->string('montant')->nullable();
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->string('bank')->nullable();
            $table->foreignId('idorder')->references('id')->on('orders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
