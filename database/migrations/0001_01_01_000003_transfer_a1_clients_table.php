<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем существование исходной таблицы
        if (!Schema::connection('artmir')->hasTable('a1_clients')) {
            return; // пропускаем перенос, если нет источника
        }

        // Отключаем проверки для ускорения
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');

        // Выполняем перенос данных одним запросом
        DB::insert("
            INSERT INTO users (
                id,
                first_name,
                last_name,
                middle_name,
                phone,
                email,
                password,
                notes,
                is_banned,
                created_at,
                updated_at,
                remember_token
            )
            SELECT
                id,
                IFNULL(name, 'Unknown') AS first_name,
                family AS last_name,
                patronymic AS middle_name,
                CASE
                    WHEN phone IS NULL OR phone = '' THEN NULL
                    ELSE REGEXP_REPLACE(phone, '[^0-9]', '')
                END AS phone,
                email,
                -- сохраняем как есть (может быть не-bcrypt). В онбоардинге логина обновим на bcrypt
                IFNULL(password, SHA2(CONCAT('temp_', id, '_', RAND()), 256)) AS password,
                comments AS notes,
                IF(blist = 1, 1, 0) AS is_banned,
                IFNULL(reg_date, NOW()) AS created_at,
                IFNULL(updated_at, NOW()) AS updated_at,
                SUBSTRING(MD5(RAND()), 1, 10) AS remember_token
            FROM artmir.a1_clients
        ");

        // Восстанавливаем проверки
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::statement('SET UNIQUE_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем только перенесенные записи
        $maxId = DB::connection('artmir')
            ->table('a1_clients')
            ->max('id');

        DB::table('users')->where('id', '<=', $maxId)->delete();
    }
};
