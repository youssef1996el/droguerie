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
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->string('qte')->nullable();
            $table->string('qte_company')->nullable();
            $table->string('qte_notification')->nullable();
            $table->decimal('price',10,2)->nullable();
            $table->string('status')->default('waiting')->nullable();
            $table->foreignId('idproduct')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('stock');
    }
};
