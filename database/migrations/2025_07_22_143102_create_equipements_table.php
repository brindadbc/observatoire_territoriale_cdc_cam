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
       Schema::create('equipements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('infrastructure_id')->constrained()->onDelete('cascade');
    $table->string('type'); // Matériel médical, Scolaire, etc.
    $table->string('nom');
    $table->integer('quantite')->default(1);
    $table->string('etat');
    $table->date('date_acquisition')->nullable();
    $table->decimal('cout', 15, 2)->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('équipements');
    }
};
