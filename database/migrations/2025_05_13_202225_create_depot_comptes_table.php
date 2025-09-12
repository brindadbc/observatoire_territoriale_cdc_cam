<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // /**
    //  * Run the migrations.
    //  */
    // public function up(): void
    // {
    //     Schema::create('depot_comptes', function (Blueprint $table) {
    //         $table->id();
    //         $table->date('date_depot');
    //         $table->string('annee_exercice');
    //         $table->boolean('validation')->default(false);
    //         $table->foreignId('receveur_id')->constrained()->onDelete('cascade');
    //          $table->foreignId('commune_id')->constrained()->onDelete('cascade');
    //         $table->timestamps();
    //     });
    // }

    // /**
    //  * Reverse the migrations.
    //  */
    // public function down(): void
    // {
    //     Schema::dropIfExists('depot_comptes');
    // }


    public function up()
    {
        Schema::create('depot_comptes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->year('annee_exercice');
            $table->enum('type', ['compte_administratif', 'compte_gestion']);
            $table->date('date_limite_depot');
            $table->date('date_depot_effectif')->nullable();
            $table->integer('jours_retard')->default(0);
            $table->enum('statut', ['en_attente', 'depose', 'approuve', 'rejete', 'non_depose'])->default('en_attente');
            $table->text('observations')->nullable();
            $table->timestamps();
            
            $table->unique(['commune_id', 'annee_exercice', 'type']);
        });
    }
    // /**
    //  * Reverse the migrations.
    //  */
    public function down(): void
    {
        Schema::dropIfExists('depot_comptes');
    }
};
