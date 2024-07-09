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
        Schema::create('import_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->uuid('import_id')->comment('インポートID');
            $table->unsignedMediumInteger('line_number')->comment('行番号');
            $table->boolean('result')->default(0)->comment('結果');
            $table->json('messages')->nullable()->comment('メッセージ');
            $table->dateTime('created_at');

            $table->foreign('import_id')->references('id')->on('imports')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_details');
    }
};
