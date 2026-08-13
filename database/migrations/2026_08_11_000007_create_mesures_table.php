<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions');
            $table->date('measured_at');
            $table->string('stage', 30);
            $table->decimal('weight', 8, 2);
            $table->decimal('waist', 8, 2)->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['inscription_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesures');
    }
};
