<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('master_classes', function (Blueprint $table) {
            $table->id();

            // Основная информация
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedInteger('price')->default(290000); // в копейках
            $table->unsignedInteger('booked_places')->default(0);

            // Медиа
            $table->string('image_path')->nullable();

            // SEO
            $table->string('page_title')->nullable();
            $table->string('meta_description', 512)->nullable(); // Более точное название для SEO

            // Организационные детали
            $table->integer('max_participants')->default(8);
            $table->boolean('is_active')->default(true);

            // Категоризация
            $table->json('tags')->nullable(); // Изменил на JSON для структурированного хранения

            $table->timestamps();

            // Индексы
            $table->index('is_active');
            $table->index('title');
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_classes');
    }
};
