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
      Schema::create('fonctionnements', function (Blueprint $table) {
    $table->id();
    $table->morphs('fonctionnable'); // Pour lier à Infrastructure ou ServiceSocial
    $table->date('date');
    $table->string('statut'); // Fonctionnel, En panne, En maintenance
    $table->text('notes')->nullable();
    $table->decimal('cout_maintenance', 15, 2)->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fonctionnements');
    }
};
