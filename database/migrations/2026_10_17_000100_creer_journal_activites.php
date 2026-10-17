<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Journal d'activité (CDC §54, audit 4.8) : ajout seul, horodatage serveur,
 * aucune modification ni suppression, même par la dirigeante.
 * Le modèle refuse déjà toute modification ; les déclencheurs ci-dessous
 * ferment aussi la porte au niveau de la base de données.
 */
return new class extends Migration
{
    private const MESSAGE = "Le journal d'activité est en ajout seul.";

    public function up(): void
    {
        Schema::create('journal_activites', function (Blueprint $table) {
            $table->id();
            // systeme, personnel ou cliente
            $table->string('auteur_type', 20);
            $table->unsignedBigInteger('auteur_id')->nullable();
            // Libellé figé au moment de l'action : « Système », « ALYA », « Administrateur · Awa »…
            $table->string('auteur_libelle', 120);
            $table->string('action', 80);
            $table->string('description', 500);
            $table->nullableMorphs('sujet');
            $table->json('donnees')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->timestamp('cree_le')->useCurrent();

            $table->index('action');
            $table->index(['auteur_type', 'auteur_id']);
            $table->index('cree_le');
        });

        $message = str_replace("'", "''", self::MESSAGE);

        match (DB::getDriverName()) {
            'mysql', 'mariadb' => $this->declencheursMysql($message),
            'sqlite' => $this->declencheursSqlite($message),
            default => null,
        };
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS journal_activites_sans_modification');
        DB::unprepared('DROP TRIGGER IF EXISTS journal_activites_sans_suppression');
        Schema::dropIfExists('journal_activites');
    }

    private function declencheursMysql(string $message): void
    {
        DB::unprepared("CREATE TRIGGER journal_activites_sans_modification BEFORE UPDATE ON journal_activites FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '{$message}'");
        DB::unprepared("CREATE TRIGGER journal_activites_sans_suppression BEFORE DELETE ON journal_activites FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '{$message}'");
    }

    private function declencheursSqlite(string $message): void
    {
        DB::unprepared("CREATE TRIGGER journal_activites_sans_modification BEFORE UPDATE ON journal_activites BEGIN SELECT RAISE(ABORT, '{$message}'); END");
        DB::unprepared("CREATE TRIGGER journal_activites_sans_suppression BEFORE DELETE ON journal_activites BEGIN SELECT RAISE(ABORT, '{$message}'); END");
    }
};
