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
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->integer('qty')->default(1)->after('price');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('amount', 'price');
            $table->integer('qty')->default(1)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropIfExists('qty');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIfExists('qty');
            $table->renameColumn('price', 'amount');
        });
    }
};
