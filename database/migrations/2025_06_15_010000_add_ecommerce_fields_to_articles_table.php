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
        Schema::table('articles', function (Blueprint $table) {
            // UGS (Unité de Gestion de Stock) / SKU (Stock Keeping Unit)
            $table->string('sku', 100)->unique()->nullable()->after('description')->index();

            // Chemin vers l'image principale (URL ou chemin local)
            $table->string('image_principale')->nullable()->after('sku');

            // Prix promotionnel / Soldes
            $table->decimal('prix_promotionnel', 8, 2)->nullable()->after('prix');

            // Statut de l'article (ex: disponible, brouillon, archivé, en_rupture_de_stock)
            $table->string('statut', 50)->default('disponible')->after('quantite')->index();

            // Poids (pour le calcul des frais de port, ex: en kg)
            $table->decimal('poids', 8, 3)->nullable()->after('statut'); // 8 chiffres au total, 3 après la virgule

            // Slug (pour des URLs conviviales)
            $table->string('slug')->unique()->nullable()->after('name')->index();

            // Visibilité (pour cacher un produit du catalogue public sans le supprimer)
            $table->boolean('est_visible')->default(true)->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('image_principale');
            $table->dropColumn('prix_promotionnel');
            $table->dropColumn('statut');
            $table->dropColumn('poids');
            $table->dropColumn('slug');
            $table->dropColumn('est_visible');
        });
    }
};
