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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->integer('store_id')->comment('店舗ID');
            $table->string('jan_cd', 20)->comment('JANコード');
            $table->integer('price')->comment('税抜販売価格');
            $table->timestamps();

            $table->unique(['store_id', 'jan_cd']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
