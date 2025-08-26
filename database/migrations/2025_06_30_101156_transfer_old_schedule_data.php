<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Проверяем существование исходных таблиц в базе artmir
        $lessonsExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables
            WHERE table_schema = 'artmir' AND table_name = 'schedule_lessons'");

        $scheduleExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables
            WHERE table_schema = 'artmir' AND table_name = 'schedule'");

        if ($lessonsExists[0]->count == 0 || $scheduleExists[0]->count == 0) {
            // Источник отсутствует — безопасно пропускаем перенос
            return;
        }

        // Проверяем существование целевых таблиц
        if (!Schema::hasTable('schedule_templates')) {
            throw new RuntimeException("Целевая таблица schedule_templates не существует");
        }

        if (!Schema::hasTable('schedule_events')) {
            throw new RuntimeException("Целевая таблица schedule_events не существует");
        }

        // Отключаем проверку внешних ключей
        Schema::disableForeignKeyConstraints();

        // Очищаем целевые таблицы
        DB::table('schedule_templates')->truncate();
        DB::table('schedule_events')->truncate();

        // Перенос шаблонов занятий из artmir.schedule_lessons
        DB::statement("
            INSERT INTO schedule_templates (
                id,
                type,
                day_of_week,
                start_time,
                teacher_id,
                is_active,
                created_at,
                updated_at
            )
            SELECT
                id,
                'regular' as type,
                day_of_week,
                time as start_time,
                teacher_id,
                status = 1 as is_active,
                NOW() as created_at,
                NOW() as updated_at
            FROM artmir.schedule_lessons
        ");

        // Перенос событий из artmir.schedule
        DB::statement("
            INSERT INTO schedule_events (
                id,
                start_datetime,
                teacher_id,
                max_participants,
                booked_count,
                is_active,
                created_at,
                updated_at
            )
            SELECT
                id,
                datetime as start_datetime,
                teacher_id,
                12 as max_participants,
                booked_places as booked_count,
                status = 1 as is_active,
                NOW() as created_at,
                NOW() as updated_at
            FROM artmir.schedule
        ");

        // Обновление template_id для событий
        DB::statement("
            UPDATE schedule_events se
            LEFT JOIN schedule_templates st ON
                DATE_FORMAT(se.start_datetime, '%W') = DATE_FORMAT(CONCAT('2000-01-01 ', st.start_time), '%W')
                AND TIME(se.start_datetime) = st.start_time
            SET se.template_id = st.id
        ");

        // Включаем проверку внешних ключей обратно
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Отключаем проверку внешних ключей
        Schema::disableForeignKeyConstraints();

        // Очищаем только если таблицы существуют
        if (Schema::hasTable('schedule_events')) {
            DB::table('schedule_events')->truncate();
        }

        if (Schema::hasTable('schedule_templates')) {
            DB::table('schedule_templates')->truncate();
        }

        // Включаем проверку внешних ключей обратно
        Schema::enableForeignKeyConstraints();
    }
};
