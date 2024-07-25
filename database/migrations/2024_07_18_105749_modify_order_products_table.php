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
            $table->dropColumn('price');
            $table->integer('price_tax')->nullable()->comment('税込価格')->change();
            $table->integer('selling_price_tax')->after('product_name')->comment('税込販売価格');
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
            $table->dropColumn(['selling_price_tax', 'wholesale_price', 'wholesale_price_tax']);
            $table->integer('price')->nullable()->after('product_name')->comment('税抜販売価格');
            $table->integer('price_tax')->nullable()->comment('税込販売価格')->change();
        });
    }
};
