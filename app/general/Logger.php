<?php

namespace app\general;

class Logger
{
	/**
	 * Логирует сообщение в заданный файл
	 *
	 * @param string $message Логируемое сообщение
	 * @param string $logFile Файл для логирования
	 * @return void
	 */
	public static function log(string $message, string $logFile = "app.log"): void
	{
		// Путь к директории логов
		$logDirectory = __DIR__ . "/../log";
		// Полный путь к файлу лога
		$logPath = $logDirectory . '/' . $logFile;

		// Проверяем, существует ли директория логов, если нет - создаем
		if (!file_exists($logDirectory)) {
			mkdir($logDirectory, 0777, true);
		}

		// Получаем текущую дату и время
		$currentTime = date('Y-m-d H:i:s');
		// Форматируем строку лога
		$log = $currentTime . ' ' . $message . "\n";
		// Добавляем запись в файл лога
		file_put_contents($logPath, $log, FILE_APPEND);
	}

    /**
     *  Логирует сообщение об ошибке в файл app.log и консольную строку
     *
     * @param string $message Сообщение
     * @return void
     */
    public static function logEchoMessage(string $message): void
    {
        self::log($message);
        echo $message . "\n";
    }

    /**
     * Логирует сообщение об ошибке в файл error.log и консольную строку
     *
     * @param string $message Сообщение об ошибке
     * @return void
     */
    public static function logEchoError(string $message): void
    {
        self::log($message, 'error.log');
        echo $message . "\n";
    }
}