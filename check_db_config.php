<?php

require_once 'vendor/autoload.php';

// Загрузим конфигурацию Laravel
$app = require_once 'bootstrap/app.php';

// Инициализируем приложение
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Проверим конфигурацию подключения artmir
$config = config('database.connections.artmir');

echo "Конфигурация подключения 'artmir':\n";
print_r($config);

// Проверим, существует ли подключение
if ($config) {
    echo "\nПодключение 'artmir' найдено в конфигурации.\n";
} else {
    echo "\nПодключение 'artmir' НЕ найдено в конфигурации.\n";
}