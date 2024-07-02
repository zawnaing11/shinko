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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->string('title', 191)->comment('タイトル');
            $table->text('body')->nullable()->comment('本文');
            $table->string('image', 191)->nullable()->comment('画像');
            $table->boolean('is_active')->default(1)->comment('有効/無効');
            $table->dateTime('publish_date')->comment('公開日時');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
