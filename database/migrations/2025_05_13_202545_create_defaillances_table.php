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
        Schema::create('defaillances', function (Blueprint $table) {
            $table->id();
            $table->string('type_defaillance');
            $table->text('description');
            $table->date('date_constat');
            $table->string('gravite');  // faible, moyenne, élevée
            $table->boolean('est_resolue')->default(false);
             $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defaillances');
    }
};
