<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('services_sociaux_de_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'sante_communautaire',
                'education_base',
                'action_sociale',
                'sport_jeunesse',
                'culture_loisirs'
            ]);
            $table->string('programme');
            $table->decimal('montant_prevu', 15, 2)->default(0);
            $table->decimal('montant_engage', 15, 2)->default(0);
            $table->integer('beneficiaires_prevus')->nullable();
            $table->integer('beneficiaires_reels')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services_sociaux_de_bases');
    }
};
