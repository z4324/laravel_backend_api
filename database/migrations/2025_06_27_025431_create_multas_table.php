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
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto', 10, 2);
            $table->string('motivo');
            $table->date('fecha_emision');
            $table->enum('estado', ['pendiente', 'pagada']);
            $table->unsignedBigInteger('huesped_id');
            $table->timestamp('fecha_notificacion')->nullable();
            $table->boolean('vista')->default(false);
            $table->timestamps();

            $table->foreign('huesped_id')->references('id')->on('huespedes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multas');
    }
};
