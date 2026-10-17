<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Panier (cahier §6.1). Une visiteuse sans compte a un panier retrouvé par
 * un jeton dans un cookie ; une cliente connectée a le panier de son
 * compte, sur tous ses appareils. À la connexion, le premier rejoint le
 * second.
 *
 * Le panier ne réserve pas le stock et ne retient pas de prix : prix et
 * disponibilité sont toujours ceux du moment.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paniers', function (Blueprint $table) {
            $table->id();
            $table->string('jeton', 64)->nullable()->unique();
            $table->foreignId('cliente_id')->nullable()->unique()->constrained('clientes')->cascadeOnDelete();
            $table->timestamps();

            $table->index('updated_at');
        });

        Schema::create('lignes_panier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panier_id')->constrained('paniers')->cascadeOnDelete();
            $table->foreignId('variante_id')->constrained('variantes')->cascadeOnDelete();
            $table->unsignedSmallInteger('quantite');
            $table->timestamps();

            $table->unique(['panier_id', 'variante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_panier');
        Schema::dropIfExists('paniers');
    }
};
