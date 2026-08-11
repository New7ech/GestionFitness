<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesure_id')->constrained('mesures')->onDelete('cascade');
            $table->foreignId('measurement_type_id')->constrained('measurement_types');
            $table->decimal('value', 8, 2);
            $table->timestamps();

            $table->unique(['mesure_id', 'measurement_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_values');
    }
};
