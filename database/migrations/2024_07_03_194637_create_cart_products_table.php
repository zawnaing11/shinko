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
        Schema::create('cart_products', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->foreignUuid('cart_id')->comment('カートID');
            $table->char('jan_cd', length: 20)->comment('JANコード');
            $table->unsignedTinyInteger('quantity')->comment('数量');
            $table->dateTimes();

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['cart_id', 'jan_cd']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_products');
    }
};
