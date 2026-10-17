<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Catalogue (cahier §5.4 et §12.2) : catégories, collections (« gammes »
 * dans le code : le mot Collection est déjà pris par Laravel), produits et
 * leurs variantes, ingrédients clés, effets recherchés, atouts affichés sur
 * la fiche, mouvements de stock.
 *
 * Les textes visibles par les clientes sont en JSON, une valeur par langue
 * ({"fr": "…", "en": "…"}). Les adresses (slug) sont communes aux langues.
 * Les prix sont des entiers en FCFA : le franc CFA n'a pas de centimes.
 * « en_ligne » et non « visible » : Eloquent a déjà une propriété $visible,
 * qui masquerait la colonne dans le code des modèles.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->json('nom');
            $table->string('slug', 120)->unique();
            $table->json('description')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('en_ligne')->default(true);
            $table->timestamps();
        });

        Schema::create('gammes', function (Blueprint $table) {
            $table->id();
            $table->json('nom');
            $table->string('slug', 120)->unique();
            // permanente, saisonniere ou edition_limitee
            $table->string('type', 20);
            $table->json('accroche')->nullable();
            $table->json('description')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('en_ligne')->default(true);
            // Dates d'affichage facultatives (collections saisonnières, éditions limitées).
            $table->timestamp('debut_le')->nullable();
            $table->timestamp('fin_le')->nullable();
            $table->timestamps();

            $table->index(['type', 'en_ligne', 'position']);
        });

        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->restrictOnDelete();
            $table->json('nom');
            $table->string('slug', 160)->unique();
            // produit ou coffret
            $table->string('nature', 20)->default('produit');
            $table->json('accroche')->nullable();
            $table->json('description')->nullable();
            // Une ligne par bienfait, une ligne par conseil.
            $table->json('bienfaits')->nullable();
            $table->json('conseils')->nullable();
            // Liste complète des ingrédients, telle qu'imprimée sur l'emballage.
            $table->json('composition')->nullable();
            // Mots-clés de recherche que la cliente ne voit pas.
            $table->json('mots_cles')->nullable();
            $table->json('meta_titre')->nullable();
            $table->json('meta_description')->nullable();
            $table->unsignedInteger('prix');
            // Coût interne facultatif, jamais montré aux clientes.
            $table->unsignedInteger('cout_interne')->nullable();
            $table->unsignedInteger('seuil_alerte')->default(5);
            $table->boolean('edition_limitee')->default(false);
            $table->timestamp('campagne_debut_le')->nullable();
            $table->timestamp('campagne_fin_le')->nullable();
            // brouillon, publie ou archive
            $table->string('statut', 20)->default('brouillon');
            // Première mise en ligne : sert aux nouveautés.
            $table->timestamp('publie_le')->nullable();
            // Nouveautés de l'accueil (cahier §5.1) : auto, toujours ou jamais.
            $table->string('affichage_nouveautes', 10)->default('auto');
            // Texte sans accents ni majuscules (noms, mots-clés, couleurs) pour la recherche.
            $table->text('texte_recherche')->nullable();
            $table->timestamps();

            $table->index(['statut', 'publie_le']);
        });

        Schema::create('variantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            // Nom de la couleur ou du format ; vide pour un produit sans choix.
            $table->json('nom')->nullable();
            // Référence interne (code article), unique.
            $table->string('reference', 60)->unique();
            // Pastille de couleur sur la fiche (#RRGGBB).
            $table->string('couleur', 7)->nullable();
            // Prix propre à la variante ; vide = prix du produit.
            $table->unsignedInteger('prix')->nullable();
            // Ne change que par un mouvement de stock (App\Support\Catalogue\Stock).
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Journal des mouvements de stock (cahier §12.2) : ajout seul.
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variante_id')->constrained('variantes')->cascadeOnDelete();
            $table->integer('quantite');
            $table->unsignedInteger('stock_apres');
            $table->string('motif', 30);
            $table->string('note', 255)->nullable();
            $table->foreignId('utilisateur_id')->nullable()->constrained('utilisateurs')->nullOnDelete();
            $table->timestamp('cree_le')->useCurrent();

            $table->index(['variante_id', 'cree_le']);
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->json('nom');
            $table->string('slug', 120)->unique();
            // Une phrase courte sous la photo : « Nourrit et protège ».
            $table->json('bienfait')->nullable();
            $table->timestamps();
        });

        Schema::create('effets', function (Blueprint $table) {
            $table->id();
            $table->json('nom');
            $table->string('slug', 120)->unique();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        // Les quatre atouts illustrés sous les boutons de la fiche (maquette 2).
        Schema::create('atouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->string('icone', 40);
            $table->json('texte');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('gamme_produit', function (Blueprint $table) {
            $table->foreignId('gamme_id')->constrained('gammes')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->primary(['gamme_id', 'produit_id']);
        });

        Schema::create('ingredient_produit', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->primary(['ingredient_id', 'produit_id']);
        });

        Schema::create('effet_produit', function (Blueprint $table) {
            $table->foreignId('effet_id')->constrained('effets')->restrictOnDelete();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->primary(['effet_id', 'produit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effet_produit');
        Schema::dropIfExists('ingredient_produit');
        Schema::dropIfExists('gamme_produit');
        Schema::dropIfExists('atouts');
        Schema::dropIfExists('effets');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('mouvements_stock');
        Schema::dropIfExists('variantes');
        Schema::dropIfExists('produits');
        Schema::dropIfExists('gammes');
        Schema::dropIfExists('categories');
    }
};
