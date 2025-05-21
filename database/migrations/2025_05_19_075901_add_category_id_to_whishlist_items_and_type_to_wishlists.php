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
        Schema::table('wishlists', function (Blueprint $table) {
            $table->enum('type', ['wants', 'needs'])->default('wants')->after('name');
        });
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIfExists('type');
        });
        Schema::table('wishlist_item', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIfExists('categories');
        });
    }
};
