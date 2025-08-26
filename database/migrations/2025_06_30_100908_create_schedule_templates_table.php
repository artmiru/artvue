<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_templates', function (Blueprint $table) {
            $table->id();

            // Тип занятия
            $table->enum('type', ['regular', 'master_class'])->default('regular');

            // Временные параметры
            $table->tinyInteger('day_of_week');
            $table->time('start_time');

            // Связь с преподавателем
            $table->foreignId('teacher_id')->nullable();
            $table->foreign('teacher_id')
                ->references('id')->on('teachers')
                ->onDelete('set null');

            // Статус
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Индексы
            $table->index(['day_of_week', 'start_time']);
            $table->index('teacher_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_templates');
    }
};
