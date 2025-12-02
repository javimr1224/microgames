<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $collection) {
            $collection->decimal('price', 8, 2)->nullable();
            $collection->string('stripe_price_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $collection) {
            $collection->dropColumn('price');
            $collection->dropColumn('stripe_price_id');
        });
    }
};