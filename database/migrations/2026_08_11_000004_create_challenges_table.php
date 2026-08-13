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
            $table->foreignId('challenge_type_id')->constrained('challenge_types');
            $table->string('label')->nullable();
            $table->date('start_date');
            $table->unsignedInteger('duration_days');
            $table->date('end_date');
            $table->unsignedInteger('capacite')->nullable();
            $table->decimal('default_price', 10, 2)->nullable();
            $table->string('status', 30)->default('planifie');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('end_date');
            $table->index('challenge_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
