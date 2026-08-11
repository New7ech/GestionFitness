<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->unique()->constrained('payments');
            $table->string('receipt_number')->unique();
            $table->dateTime('issued_at');
            $table->string('participante_full_name');
            $table->string('challenge_type_label');
            $table->unsignedInteger('challenge_duration_days');
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('amount_remaining', 10, 2);
            $table->string('payment_mode', 50);
            $table->string('issued_by_name')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recus');
    }
};
