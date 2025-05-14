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
        Schema::create('receveurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
             $table->string('statut');
              $table->string('matricule');
            $table->date('date_prise_fonction');
            $table->string('telephone')->nullable();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receveurs');
    }
};
