<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participante_id')->constrained('participantes');
            $table->foreignId('challenge_id')->constrained('challenges');
            $table->date('inscription_date');
            $table->string('status', 30)->default('reservee');
            $table->text('goal_text')->nullable();
            $table->decimal('goal_weight', 8, 2)->nullable();
            $table->decimal('goal_waist', 8, 2)->nullable();
            $table->text('goal_personal')->nullable();
            $table->text('observations')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('payment_status', 50)->default('impaye');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['participante_id', 'status']);
            $table->index(['challenge_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
