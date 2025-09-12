<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'vehicules_utilitaires',
                'materiel_informatique',
                'mobilier_bureau',
                'equipement_technique',
                'materiel_collecte'
            ]);
            $table->string('designation');
            $table->decimal('montant_prevu', 15, 2)->default(0);
            $table->decimal('montant_engage', 15, 2)->default(0);
            $table->integer('quantite')->default(1);
            $table->enum('statut', ['planifie', 'commande', 'livre', 'operationnel'])->default('planifie');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('equipements');
    }
  
};
