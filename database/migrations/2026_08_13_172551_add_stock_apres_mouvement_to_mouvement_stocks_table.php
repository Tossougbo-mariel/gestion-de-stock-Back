<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            $table->integer('stock_apres_mouvement')->nullable()->after('quantite');
        });
    }

    public function down(): void
    {
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            $table->dropColumn('stock_apres_mouvement');
        });
    }
};