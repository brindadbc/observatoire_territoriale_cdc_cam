<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluationToTauxRealisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taux_realisations', function (Blueprint $table) {
            $table->string('evaluation', 20)->nullable()->after('pourcentage');
            $table->decimal('ecart', 15, 2)->nullable()->after('evaluation');
            $table->timestamp('date_calcul')->nullable()->after('ecart');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taux_realisations', function (Blueprint $table) {
            $table->dropColumn(['evaluation', 'ecart', 'date_calcul']);
        });
    }
}