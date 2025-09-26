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
       Schema::table('depot_comptes', function (Blueprint $table) {
            // Si la colonne n'existe pas
            if (!Schema::hasColumn('depot_comptes', 'receveur_id')) {
                $table->foreignId('receveur_id')
                      ->nullable()
                      ->constrained('receveurs')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('depot_comptes', function (Blueprint $table) {
            $table->dropForeign(['receveur_id']);
            $table->dropColumn('receveur_id');
        });
    }
};
