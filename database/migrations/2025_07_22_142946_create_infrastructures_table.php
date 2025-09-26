<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('infrastructures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('localisation');
            $table->string('etat');
            $table->date('date_construction')->nullable();
            $table->decimal('cout_construction', 15, 2)->nullable();
            $table->timestamps();
            
            // Optional: Add index for better performance on frequently queried columns
            $table->index(['commune_id', 'type', 'etat']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('infrastructures');
    }
};
