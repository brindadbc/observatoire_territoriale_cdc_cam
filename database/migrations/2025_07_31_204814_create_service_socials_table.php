<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->integer('capacite')->nullable();
            $table->integer('personnel')->nullable();
            $table->decimal('budget_annuel', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_socials');
    }
};