<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clientes de la boutique. Le téléphone est l'identité réelle (D10) :
        // obligatoire, unique, enregistré au format international (+221…).
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('prenom', 80);
            $table->string('nom', 80);
            $table->string('telephone', 20)->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('langue', 2)->default('fr');
            $table->rememberToken();
            $table->timestamps();
        });

        // Personnel : dirigeante, administrateurs, livreurs. Les rôles viennent
        // de spatie/laravel-permission (garde « personnel »).
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('prenom', 80);
            $table->string('nom', 80);
            $table->string('email')->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('password');
            $table->boolean('actif')->default(true);
            // Double authentification par application (Filament), chiffrée.
            $table->text('app_authentication_secret')->nullable();
            $table->text('app_authentication_recovery_codes')->nullable();
            $table->timestamp('derniere_connexion_le')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('password_reset_tokens_personnel', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Accords donnés par la cliente (audit 4.4), avec date et origine.
        // Chaque changement est une nouvelle ligne : l'historique se lit tel quel.
        Schema::create('consentements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('type', 40);
            $table->boolean('accorde');
            $table->string('origine', 40);
            $table->string('adresse_ip', 45)->nullable();
            $table->timestamp('cree_le')->useCurrent();

            $table->index(['cliente_id', 'type']);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('consentements');
        Schema::dropIfExists('password_reset_tokens_personnel');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('utilisateurs');
        Schema::dropIfExists('clientes');
    }
};
