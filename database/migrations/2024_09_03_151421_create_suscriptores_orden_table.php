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
        Schema::create('suscriptores_orden', function (Blueprint $table) {
            $table->id();
            $table->string('numero_if');
            $table->foreign('numero_if')->references('numero_if')->on('expedientes')->onDelete('cascade');
            $table->integer('orden')->nullable();
            $table->string('pagina')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscriptores_orden');
    }
};
