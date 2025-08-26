<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_events', function (Blueprint $table) {
            $table->id();

            // Временные параметры
            $table->dateTime('start_datetime');

            // Связь с шаблоном
            $table->foreignId('template_id')->nullable();
            $table->foreign('template_id')
                ->references('id')->on('schedule_templates')
                ->onDelete('set null');

            // Связь с преподавателем
            $table->foreignId('teacher_id')->nullable();
            $table->foreign('teacher_id')
                ->references('id')->on('teachers')
                ->onDelete('set null');

            // Связь с мастер-классом (опционально)
            $table->foreignId('master_class_id')->nullable();

            // Параметры бронирования
            $table->unsignedInteger('booked_count')->default(0);
            $table->unsignedInteger('max_participants')->default(4);

            // Статус
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Индексы
            $table->unique(['template_id', 'start_datetime']);
            $table->index('start_datetime');
            $table->index('teacher_id');
            $table->index('template_id');
            $table->index('master_class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_events');
    }
};
