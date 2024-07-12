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
        Schema::create('reglements', function (Blueprint $table) {
            $table->id();

            $table->decimal('total',10,2)->nullable();
            $table->string('datepaiement')->nullable();
            $table->foreignId('idclient')->references('id')->on('clients')->onDelete('cascade');
            $table->foreignId('idorder')->references('id')->on('orders')->onDelete('cascade');
            $table->foreignId('idmode')->references('id')->on('modepaiement')->onDelete('cascade');
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
        Schema::dropIfExists('reglements');
    }
};
