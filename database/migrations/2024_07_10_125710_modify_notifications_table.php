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
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dateTime('publish_end_datetime')->nullable()->after('publish_date')->comment('公開終了日時');
        });

        DB::statement('ALTER TABLE notifications CHANGE publish_date publish_begin_datetime DATETIME NOT NULL COMMENT \'公開開始日時\';');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('publish_end_datetime');
            $table->boolean('is_active')->default(1)->after('image')->comment('有効/無効');
        });

        DB::statement('ALTER TABLE notifications CHANGE publish_begin_datetime publish_date DATETIME COMMENT \'公開日時\';');
    }
};
