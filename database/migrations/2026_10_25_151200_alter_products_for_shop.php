<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique();
            $table->string('summary')->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('track_stock')->default(true);
            $table->boolean('is_published')->default(true);
            $table->string('lot')->nullable();
            $table->text('storage')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'summary', 'stock', 'track_stock', 'is_published', 'lot', 'storage']);
        });
    }
};