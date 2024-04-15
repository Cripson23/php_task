<?php

namespace app\services;

use app\general\Logger;

class HawkingTaskThree
{
    /**
     * Сканирует заданную директорию на наличие файлов с расширением .bros,
     * имена которых состоят из цифр и букв латинского алфавита.
     * Выводит отсортированный список подходящих файлов.
     */
    public static function listFilteredFiles(): void
    {
        $directory = __DIR__ . "/../hawking";
        $pattern = '/^[a-zA-Z0-9]+\.bros$/'; // Регулярное выражение для фильтрации файлов

        // Проверяем наличие директории
        if (!file_exists($directory)) {
            Logger::logEchoError('Директория hawking не найдена.');
            return;
        }

        // Получаем список файлов в директории
        $files = scandir($directory);

        // Массив для хранения подходящих файлов
        $filteredFiles = [];

        // Перебираем файлы и проверяем их имена с помощью регулярного выражения
        foreach ($files as $file) {
            if (preg_match($pattern, $file)) {
                $filteredFiles[] = $file;
            }
        }

        if (count($filteredFiles) > 0) {
            // Сортируем отфильтрованные файлы по имени
            sort($filteredFiles);

            // Выводим отсортированный список файлов
            foreach ($filteredFiles as $file) {
                Logger::logEchoMessage($file);
            }
        } else {
            Logger::logEchoMessage('Подходящих файлов не найдено!');
        }
    }
}
