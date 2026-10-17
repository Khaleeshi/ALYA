<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Codes de connexion envoyés par SMS (cahier §6.2 et §15). Le code lui-même
 * n'est jamais enregistré : seulement son empreinte. Les lignes de plus d'un
 * jour sont supprimées chaque nuit (model:prune).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codes_connexion', function (Blueprint $table) {
            $table->id();
            $table->string('telephone', 20)->index();
            $table->char('empreinte', 64);
            $table->unsignedTinyInteger('tentatives')->default(0);
            $table->timestamp('expire_le');
            $table->timestamp('utilise_le')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->timestamp('cree_le')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codes_connexion');
    }
};
