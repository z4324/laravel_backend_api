<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('token_id')->nullable();
            $table->string('token', 80)->unique();
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('inicio')->useCurrent();
            $table->enum('estado', ['activa', 'inactiva'])->default('activa');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('huespedes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};