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
        Schema::create('autres_ressources', function (Blueprint $table) {
            $table->id();
          $table->foreignId('commune_id')->constrained()->onDelete('cascade');
    $table->string('source');
    $table->string('type_ressource');
    $table->decimal('montant', 15, 2);
    $table->date('date_reception');
    $table->string('description');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autres_ressources');
    }
};
