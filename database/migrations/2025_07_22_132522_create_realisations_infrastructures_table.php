<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      public function up()
    {
        Schema::create('realisations_infrastructures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'routes_voiries',
                'batiments_publics',
                'adduction_eau',
                'assainissement',
                'electrification',
                'ponts_dalots'
            ]);
            $table->string('designation');
            $table->decimal('montant_prevu', 15, 2)->default(0);
            $table->decimal('montant_engage', 15, 2)->default(0);
            $table->decimal('taux_execution', 5, 2)->default(0);
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'suspendu'])->default('planifie');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('realisations_infrastructures');
    }
};
