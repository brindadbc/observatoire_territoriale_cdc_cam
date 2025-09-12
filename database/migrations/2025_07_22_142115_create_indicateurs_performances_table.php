<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('indicateurs_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->year('annee');
            $table->decimal('taux_execution_budget', 5, 2)->default(0);
            $table->decimal('taux_recouvrement_recettes', 5, 2)->default(0);
            $table->decimal('autonomie_financiere', 5, 2)->default(0);
            $table->decimal('endettement_par_habitant', 10, 2)->default(0);
            $table->decimal('investissement_par_habitant', 10, 2)->default(0);
            $table->integer('score_gouvernance')->default(0); // Sur 100
            $table->enum('niveau_performance', ['excellent', 'bon', 'moyen', 'faible', 'critique'])->default('moyen');
            $table->timestamps();
            
            $table->unique(['commune_id', 'annee']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('indicateurs_performance');
    }
};
