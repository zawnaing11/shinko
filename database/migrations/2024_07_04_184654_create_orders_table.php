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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->foreignUuid('user_id')->nullable()->comment('ユーザーID');
            $table->integer('store_id')->nullable()->comment('店舗ID');
            $table->string('user_name', length: 191)->comment('ユーザー名');
            $table->string('store_name', length: 191)->comment('店舗名');
            $table->dateTimes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('store_id')->references('id')->on(DB::connection('mysql_shinko')->getDatabaseName() . '.store')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
