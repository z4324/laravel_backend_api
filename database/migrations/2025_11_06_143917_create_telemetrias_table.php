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
        Schema::create('telemetrias', function (Blueprint $table) {
            $table->id();
            $table->string('trabajador_id');
            $table->integer('hr'); // Frecuencia cardíaca
            $table->float('baro'); // Presión barométrica
            $table->float('accX'); // Acelerómetro X
            $table->float('accY'); // Acelerómetro Y
            $table->float('accZ'); // Acelerómetro Z
            $table->timestamp('timestamp'); // Timestamp del dato
            $table->timestamps(); // created_at y updated_at
            
            // Índices para búsquedas eficientes
            $table->index('trabajador_id');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telemetrias');
    }
};
