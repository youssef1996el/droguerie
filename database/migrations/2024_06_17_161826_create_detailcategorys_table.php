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
        Schema::create('detailcategorys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('idcategory')->references('id')->on('categorys')->onDelete('cascade');
            $table->foreignId('idcompany')->references('id')->on('company')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailcategorys');
    }
};
