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
        Schema::create('linedevis', function (Blueprint $table) {
            $table->id();
            $table->integer('qte')->nullable();
            $table->decimal('price',10,2)->nullable();
            $table->decimal('total',10,2)->nullable();
            $table->decimal('accessoire',10,2)->default(0)->nullable();
            $table->integer('idsetting')->nullable();
            $table->integer('idstock')->nullable();
            $table->foreignId('idproduct')->references('id')->on('products')->onDelete('cascade');
            $table->foreignId('iddevis')->references('id')->on('devis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linedevis');
    }
};
