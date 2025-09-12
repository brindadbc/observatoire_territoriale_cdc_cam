<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projet_financements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained()->onDelete('cascade');
            $table->string('source'); // Etat, partenaires, autofinancement, etc.
            $table->decimal('montant', 15, 2);
            $table->enum('type', ['subvention', 'pret', 'don', 'autofinancement']);
            $table->date('date_obtention')->nullable();
            $table->text('conditions')->nullable();
            $table->timestamps();
            
            $table->index('projet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_financements');
    }
};
