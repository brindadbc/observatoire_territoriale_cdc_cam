<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->year('annee');
            $table->decimal('montant_total_ressources', 15, 2)->default(0);
            $table->decimal('montant_total_emplois', 15, 2)->default(0);
            $table->decimal('taux_execution', 5, 2)->default(0);
            $table->enum('statut', ['previsionnel', 'adopte', 'execute', 'clos'])->default('previsionnel');
            $table->date('date_adoption')->nullable();
            $table->timestamps();
            
            $table->unique(['commune_id', 'annee']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('budgets');
    }
};
