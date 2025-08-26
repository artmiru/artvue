<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название пакета ("4 урока", "Безлимитный" и т.д.)
            $table->integer('lesson_count')->nullable(); // Количество уроков (null для безлимитного)
            $table->unsignedInteger('price'); // Цена пакета в копейках
            $table->integer('validity_days')->default(30); // Срок действия в днях
            $table->text('description')->nullable(); // Описание пакета
            $table->integer('sort_order')->default(0); // Порядок сортировки
            $table->boolean('is_active')->default(true); // Активен ли пакет
            $table->timestamps();
            $table->softDeletes(); // Мягкое удаление
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
