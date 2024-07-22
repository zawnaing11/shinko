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
        Schema::table('order_products', function (Blueprint $table) {
            $table->after('list_price_tax', function ($table) {
                $table->decimal('wholesale_price', 8, 2)->default(0.00)->comment('税抜卸値');
                $table->decimal('wholesale_price_tax', 8, 2)->default(0.00)->comment('税込卸値');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'wholesale_price_tax']);
        });
    }
};
