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
        Schema::create('depot_comptes', function (Blueprint $table) {
            $table->id();
            $table->date('date_depot');
            $table->string('annee_exercice');
            $table->boolean('validation')->default(false);
            $table->foreignId('receveur_id')->constrained()->onDelete('cascade');
             $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depot_comptes');
    }
};
