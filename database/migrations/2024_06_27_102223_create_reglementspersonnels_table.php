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
        Schema::create('reglementspersonnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idpersonnel')->references('id')->on('personnels')->onDelete('cascade');
            $table->decimal('total',10,2)->nullabale();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglementspersonnels');
    }
};
