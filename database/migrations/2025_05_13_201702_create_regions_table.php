<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

 return new class extends Migration
{
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('regions', function (Blueprint $table) {
//             $table->id();
//             $table->string('nom');
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('regions');
//     }
// };

 public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code', 10)->unique();
            $table->string('chef_lieu');
            $table->integer('nombre_departements')->default(0);
            $table->integer('nombre_communes')->default(0);
            $table->decimal('superficie', 10, 2)->nullable();
            $table->integer('population')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('regions');
    }
};
