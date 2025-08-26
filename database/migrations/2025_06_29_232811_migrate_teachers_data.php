<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateTeachersData extends Migration
{
    public function up()
    {
        // Проверяем существование исходной таблицы в базе artmir
        $sourceExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables
                                  WHERE table_schema = 'artmir' AND table_name = 'teachers'");

        if ($sourceExists[0]->count == 0) {
            throw new RuntimeException("Исходная таблица teachers не существует в базе artmir");
        }

        // Проверяем существование целевой таблицы в текущей базе
        if (!Schema::hasTable('teachers')) {
            throw new RuntimeException("Целевая таблица teachers не существует в текущей базе");
        }

        // Очищаем целевую таблицу
        Schema::disableForeignKeyConstraints();
        DB::table('teachers')->truncate();
        Schema::enableForeignKeyConstraints();

        // Переносим только записи с заполненным user_id
        DB::statement("
            INSERT INTO teachers (
                id,
                user_id,
                about,
                phone,
                folder,
                keypass_code,
                created_at,
                deleted_at,
                updated_at
            )
            SELECT
                id,
                user_id,
                about,
                phone,
                folder,
                teacher_keypass as keypass_code,
                created_at,
                CASE WHEN status = 1 THEN NULL ELSE NOW() END as deleted_at,
                NOW() as updated_at
            FROM artmir.teachers
            WHERE user_id IS NOT NULL AND user_id != ''
        ");
    }

    public function down()
    {
        // Откат миграции - очистка таблицы teachers
        Schema::disableForeignKeyConstraints();
        DB::table('teachers')->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
