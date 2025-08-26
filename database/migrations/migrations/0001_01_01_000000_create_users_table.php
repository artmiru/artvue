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
        // Создаём модифицированную таблицу users с аутентификацией по телефону
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Основные поля
            $table->string('first_name')->comment('Имя пользователя');
            $table->string('last_name')->nullable()->comment('Фамилия');
            $table->string('middle_name')->nullable()->comment('Отчество');

            // Аутентификация по телефону
            $table->string('phone', 10)->unique()->comment('10 цифр номера без префикса');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Дополнительные поля
            $table->enum('role', ['student', 'teacher', 'admin', 'boss'])->default('student');
            $table->text('notes')->nullable();
            $table->boolean('is_banned')->default(false);
            $table->string('yandex_metrica_uid', 32)->nullable()
                ->comment('Yandex Metrika UID');

            // Timestamps
            $table->timestamps();

            // Индексы
            $table->index('phone', 'users_phone_index');
            $table->index('last_name', 'users_last_name_index');
            $table->index(['last_name', 'first_name'], 'users_last_first_index');
            $table->index(['first_name', 'last_name'], 'users_first_last_index');
            $table->fullText(['last_name', 'first_name', 'middle_name'], 'users_fulltext_index')
                ->comment('Для нечёткого поиска по ФИО');
        });

        // Стандартные таблицы Laravel
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};


// INSERT INTO users (
//     id,
//     first_name,
//     last_name,
//     middle_name,
//     phone,
//     email,
//     password,
//     notes,
//     is_banned,
//     created_at,
//     updated_at,
//     remember_token
// )
// SELECT
//     id,
//     IFNULL(name, 'Unknown') AS first_name,
//     family AS last_name,
//     patronymic AS middle_name,
//     CASE
//         WHEN phone IS NULL OR phone = '' THEN NULL
//         ELSE REGEXP_REPLACE(phone, '[^0-9]', '')
//     END AS phone,
//     email,
//     IFNULL(password, SHA2(CONCAT('temp_', id, '_', RAND()), 256)) AS password,
//     comments AS notes,
//     IF(blist = 1, 1, 0) AS is_banned,
//     IFNULL(reg_date, NOW()) AS created_at,
//     IFNULL(updated_at, NOW()) AS updated_at,
//     SUBSTRING(MD5(RAND()), 1, 10) AS remember_token
// FROM artmir.a1_clients;
