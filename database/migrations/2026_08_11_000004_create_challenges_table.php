<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participante_id')->constrained('participantes');
            $table->foreignId('challenge_type_id')->constrained('challenge_types');
            $table->date('start_date');
            $table->unsignedInteger('duration_days');
            $table->date('end_date');
            $table->string('status', 30)->default('planifie');
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
            $table->index('end_date');
            $table->index('challenge_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
