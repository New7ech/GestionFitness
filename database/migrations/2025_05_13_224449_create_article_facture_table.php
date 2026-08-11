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
        Schema::create('article_facture', function (Blueprint $table) {
            $table->unsignedBigInteger('facture_id');
            $table->unsignedBigInteger('article_id');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 8, 2);
            $table->timestamps();

            $table->foreign('facture_id')
                  ->references('id')->on('factures')
                  ->onDelete('cascade');
            $table->foreign('article_id')
                  ->references('id')->on('articles')
                  ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_facture');
    }
};
