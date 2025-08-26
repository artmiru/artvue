<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Транслитерация русского текста по системе B (ГОСТ 7.79-2000)
     */
    protected function transliterate(string $string): string
    {
        $converter = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'kh',
            'ц' => 'ts',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shch',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',

            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'Yo',
            'Ж' => 'Zh',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'Y',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'Kh',
            'Ц' => 'Ts',
            'Ч' => 'Ch',
            'Ш' => 'Sh',
            'Щ' => 'Shch',
            'Ъ' => '',
            'Ы' => 'Y',
            'Ь' => '',
            'Э' => 'E',
            'Ю' => 'Yu',
            'Я' => 'Ya'
        ];

        return strtr($string, $converter);
    }

    /**
     * Извлекаем имя файла без расширения
     */
    protected function getFilenameWithoutExtension(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $filename = pathinfo($path, PATHINFO_FILENAME);
        return $filename === '.' ? null : $filename;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем существование исходной таблицы
        $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables
                                 WHERE table_schema = 'artmir' AND table_name = 'mk_img'");

        if ($tableExists[0]->count == 0) {
            throw new RuntimeException("Исходная таблица mk_img не существует в базе artmir");
        }

        // Проверяем существование целевой таблицы
        if (!Schema::hasTable('master_classes')) {
            throw new RuntimeException("Целевая таблица master_classes не существует");
        }

        // Отключаем проверку внешних ключей
        Schema::disableForeignKeyConstraints();
        DB::table('master_classes')->truncate();
        Schema::enableForeignKeyConstraints();

        // Получаем данные из старой таблицы
        $classes = DB::connection('artmir')->table('mk_img')->get();

        // Собираем уникальные slug
        $usedSlugs = [];
        $data = [];

        foreach ($classes as $class) {
            // Транслитерация и генерация slug
            $transliterated = $this->transliterate($class->title);
            $baseSlug = Str::slug($transliterated);
            $slug = $baseSlug;
            $counter = 1;

            // Проверяем уникальность slug
            while (in_array($slug, $usedSlugs)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $usedSlugs[] = $slug;

            // Обработка пути изображения
            $imagePath = $this->getFilenameWithoutExtension($class->src);

            $data[] = [
                'id' => $class->id,
                'title' => $class->title,
                'slug' => $slug,
                'image_path' => $imagePath, // Только имя файла без расширения
                'page_title' => $class->title, // Используем заголовок как page_title
                'meta_description' => null,
                'max_participants' => 8,
                'is_active' => $class->state == 1,
                'tags' => null,
                'created_at' => $class->created_at ?? now(),
                'updated_at' => $class->updated_at ?? now(),
            ];
        }

        // Вставляем данные порциями по 200 записей
        foreach (array_chunk($data, 200) as $chunk) {
            DB::table('master_classes')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('master_classes')->truncate();
        Schema::enableForeignKeyConstraints();
    }
};
