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
        Schema::create('suscriptores', function (Blueprint $table) {
            $table->id();
            $table->string('numero_if');
            $table->foreign('numero_if')->references('numero_if')->on('expedientes')->onDelete('cascade');
            $table->string('expte_nro')->nullable();
            $table->string('suscriptor_original')->nullable();
            $table->string('le')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscriptores');
    }
};
