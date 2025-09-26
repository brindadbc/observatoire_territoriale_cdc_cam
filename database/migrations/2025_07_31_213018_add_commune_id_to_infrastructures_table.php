<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            // First check if column doesn't exist already
            if (!Schema::hasColumn('infrastructures', 'commune_id')) {
                $table->foreignId('commune_id')->nullable()->constrained()->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            $table->dropForeign(['commune_id']);
            $table->dropColumn('commune_id');
        });
    }
};