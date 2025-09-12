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
        Schema::create('donations_extérieures', function (Blueprint $table) {
            $table->id();
         $table->foreignId('commune_id')->constrained()->onDelete('cascade');
    $table->string('donateur');
    $table->string('type_aide');
    $table->decimal('montant', 15, 2);
    $table->string('description');
    $table->date('date_reception');
    $table->string('conditions')->nullable();
    $table->string('projet_associe')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_exterieurs');
    }
};