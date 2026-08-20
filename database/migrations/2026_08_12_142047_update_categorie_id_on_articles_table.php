<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['categorie_id']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('categorie_id')->nullable(false)->change();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreign('categorie_id')
                  ->references('id')->on('categories')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['categorie_id']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('categorie_id')->nullable()->change();
            $table->foreign('categorie_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null');
        });
    }
};