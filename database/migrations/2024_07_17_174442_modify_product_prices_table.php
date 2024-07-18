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
        Schema::table('product_prices', function (Blueprint $table) {
            $table->renameColumn('price', 'price_tax');
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->integer('price_tax')->comment('税込販売価格')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->renameColumn('price_tax', 'price');
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->integer('price')->comment('税抜販売価格')->change();
        });
    }
};
