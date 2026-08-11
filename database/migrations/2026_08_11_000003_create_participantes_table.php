<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email')->nullable()->unique();
            $table->string('address')->nullable();
            $table->string('photo_path')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('status', 30)->default('active');
            $table->boolean('has_cesarean')->nullable();
            $table->text('cesarean_comment')->nullable();
            $table->text('health_notes')->nullable();
            $table->date('registration_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['last_name', 'first_name']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participantes');
    }
};
