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
        Schema::table('users', function (Blueprint $collection) {
            if (Schema::getConnection()->getDriverName() === 'mongodb') {
                $collection->array('purchased_game_ids')->default([]);

                return;
            }

            $collection->json('purchased_game_ids')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $collection) {
            $collection->dropColumn('purchased_game_ids');
        });
    }
};
