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
        Schema::create('infos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('ice')->nullable();
            $table->string('phone')->nullable();
            $table->string('fix')->nullable();
            $table->string('cnss')->nullable();
            $table->string('rc')->nullable();
            $table->string('if')->nullable();
            $table->string('address')->nullable();
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
        Schema::dropIfExists('infos');
    }
};
