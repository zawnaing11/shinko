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
        Schema::create('imports', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->string('model_name', length: 191)->comment('モデル名');
            $table->string('file_name', length: 191)->comment('ファイル名');
            $table->unsignedTinyInteger('status')->default(1)->comment('ステータス');
            $table->json('messages')->nullable()->comment('メッセージ');
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
