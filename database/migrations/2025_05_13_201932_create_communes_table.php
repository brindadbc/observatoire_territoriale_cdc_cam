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
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('population')->nullable();
            $table->decimal('superficie', 10, 2)->nullable();
            $table->text('adresse')->nullable();
            $table->string('coordonnees_gps')->nullable();
            $table->foreignId('departement_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('code');
            $table->index('departement_id');
            $table->index(['nom', 'departement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};



// <?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('communes', function (Blueprint $table) {
//             $table->id();
//            $table->string('nom');
//             $table->string('code')->unique();
//              $table->string('telephone')->nullable();
//             $table->foreignId('departement_id')->constrained()->onDelete('cascade');
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('communes');
//     }
// };
