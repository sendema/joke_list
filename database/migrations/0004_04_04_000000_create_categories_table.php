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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // название категории
            $table->string('slug')->unique();  // URL-friendly название
            $table->timestamps();
        });

        // добавляем внешний ключ в таблицу jokes
        Schema::table('jokes', function (Blueprint $table) {
            $table->foreign('id_category')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // сначала удаляем внешний ключ
        Schema::table('jokes', function (Blueprint $table) {
            $table->dropForeign(['id_category']);
        });

        Schema::dropIfExists('categories');
    }
};
