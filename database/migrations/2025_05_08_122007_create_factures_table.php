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
        // Création de la table des factures
        Schema::create('factures', function (Blueprint $table) {
            $table->id();

            // Infos client
            $table->string('client_nom');
            $table->string('client_prenom')->nullable();
            $table->string('client_adresse')->nullable();
            $table->string('client_telephone')->nullable();
            $table->string('client_email')->nullable();

            // Infos facture
            $table->date('date_facture');
            $table->decimal('montant_ht', 10, 2);      // Hors taxe
            $table->decimal('tva', 5, 2)->default(18); // TVA en %, exemple : 20%
            $table->decimal('montant_ttc', 10, 2);     // TTC = HT + TVA

            // Paiement
            $table->string('mode_paiement', 50)->nullable(); // Ex : carte, chèque, espèces
            $table->string('statut_paiement', 50)->default('non payé'); // Ex : payé, partiel, en attente
            $table->date('date_paiement')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
