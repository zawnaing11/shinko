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
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->foreignUuid('user_id')->comment('ユーザーID');
            $table->integer('store_id')->comment('店舗ID');
            $table->dateTime('created_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('store_id')->references('id')->on(DB::connection('mysql_shinko')->getDatabaseName() . '.store')->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['user_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
