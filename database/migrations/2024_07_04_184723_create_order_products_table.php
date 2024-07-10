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
        Schema::create('order_products', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->foreignUuid('order_id')->comment('注文ID');
            $table->char('jan_cd', length: 20)->comment('JANコード');
            $table->unsignedTinyInteger('quantity')->comment('数量');
            $table->string('product_name', length: 191)->comment('商品名');
            $table->integer('price')->nullable()->comment('税抜販売価格');
            $table->integer('price_tax')->nullable()->comment('税込販売価格');
            $table->integer('list_price')->comment('税抜販売価格');
            $table->integer('list_price_tax')->comment('税込販売価格');
            $table->decimal('tax_rate', 5, 2)->default(0.00)->comment('消費税率');
            $table->dateTimes();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['order_id', 'jan_cd']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
