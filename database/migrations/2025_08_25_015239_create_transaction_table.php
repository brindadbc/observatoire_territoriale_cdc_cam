<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['recette', 'depense', 'transfert', 'remboursement']);
            $table->decimal('montant', 15, 2);
            $table->text('description')->nullable();
            $table->date('date_transaction');
            $table->string('reference')->unique();
            $table->enum('statut', ['en_attente', 'valide', 'rejete', 'annule'])->default('en_attente');
            $table->year('annee_exercice');
            $table->timestamps();
            
            $table->index(['commune_id', 'annee_exercice']);
            $table->index(['date_transaction', 'type']);
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};