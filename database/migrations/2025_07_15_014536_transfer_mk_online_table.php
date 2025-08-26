<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function toMinorUnits($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $normalized = str_replace(',', '.', (string) $value);
        return (int) round(((float) $normalized) * 100);
    }

    protected function toArrayList($value): array
    {
        if ($value === null) {
            return [];
        }
        $str = trim((string) $value);
        if ($str === '') {
            return [];
        }
        // If already JSON array
        $decoded = json_decode($str, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        // Fallback: comma-separated
        $parts = array_map('trim', explode(',', $str));
        $parts = array_values(array_filter($parts, fn($x) => $x !== ''));
        return $parts;
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем существование таблиц
        $this->checkTablesExist();

        // Отключаем внешние ключи для безопасности
        Schema::disableForeignKeyConstraints();

        // Очищаем целевую таблицу
        DB::table('online_master_classes')->truncate();

        // Переносим данные порционно в PHP, чтобы конвертировать значения под новые типы
        $this->transferDataInChunks();

        // Включаем внешние ключи обратно
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('online_master_classes')->truncate();
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Проверяет существование необходимых таблиц
     */
    protected function checkTablesExist(): void
    {
        $sourceExists = DB::selectOne("
            SELECT COUNT(*) as count
            FROM information_schema.tables
            WHERE table_schema = 'artmir'
            AND table_name = 'mk_online'
        ");

        if (!$sourceExists || $sourceExists->count == 0) {
            // Источник отсутствует — безопасно пропускаем перенос
            return;
        }

        if (!Schema::hasTable('online_master_classes')) {
            return;
        }
    }

    /**
     * Перенос данных одним запросом (для баз на одном сервере)
     */
    protected function transferDataWithSingleQuery(): void {}

    /**
     * Перенос данных порциями (для разных серверов)
     */
    protected function transferDataInChunks(): void
    {
        $chunkSize = 100;
        $totalRecords = DB::connection('artmir')
            ->table('mk_online')
            ->count();

        $processed = 0;

        while ($processed < $totalRecords) {
            $rows = DB::connection('artmir')
                ->table('mk_online')
                ->select('*')
                ->skip($processed)
                ->take($chunkSize)
                ->get();

            $payload = [];

            foreach ($rows as $row) {
                $payload[] = [
                    'id' => $row->id,
                    'img' => $row->img,
                    'title' => $row->title,
                    'level' => (int) $row->level,
                    'duration_minutes' => null,
                    'price' => $this->toMinorUnits($row->price),
                    'link' => $row->link,
                    'paid_link' => $row->paid_link,
                    'teacher_id' => $row->teacher_id,
                    'colors' => json_encode($this->toArrayList($row->colors)),
                    'materials' => json_encode($this->toArrayList($row->materials)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($payload)) {
                DB::table('online_master_classes')->insert($payload);
                $processed += count($payload);
            }
        }
    }

    /**
     * Проверяет, находятся ли базы на одном сервере
     */
    protected function isSameDatabaseServer(): bool
    {
        try {
            DB::statement("SELECT 1 FROM artmir.mk_online LIMIT 1");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
};
