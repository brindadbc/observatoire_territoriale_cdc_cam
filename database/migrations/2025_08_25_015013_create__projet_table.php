<?php
// Migration create_projets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_debut');
            $table->date('date_fin_prevue');
            $table->date('date_fin_reelle')->nullable();
            $table->decimal('budget', 15, 2);
            $table->decimal('cout_reel', 15, 2)->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'suspendu', 'termine', 'annule'])->default('planifie');
            $table->enum('priorite', ['basse', 'normale', 'elevee', 'critique'])->default('normale');
            $table->integer('pourcentage_completion')->default(0);
            $table->timestamps();
            
            $table->index(['commune_id', 'statut']);
            $table->index(['date_debut', 'date_fin_prevue']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};