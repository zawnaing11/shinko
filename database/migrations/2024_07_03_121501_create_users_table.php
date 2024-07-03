<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('ID');
            $table->integer('company_id')->comment('企業ID');
            $table->string('email', length: 191)->comment('Eメールアドレス');
            $table->char('password', length: 60)->comment('パスワード');
            $table->string('name', length: 191)->comment('氏名');
            $table->boolean('is_active')->default(1)->comment('有効/無効');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->foreign('company_id')->references('id')->on(DB::connection('mysql_shinko')->getDatabaseName() . '.company')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
