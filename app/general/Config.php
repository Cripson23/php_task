<?php

namespace app\general;

/**
 * Класс конфигурации для приложения.
 */
class Config
{
	/**
	 * Получение параметров подключения к базе данных.
	 *
	 * Этот метод возвращает ассоциативный массив с конфигурационными параметрами
	 * для подключения к базе данных, которые извлекаются из переменных окружения.
	 *
	 * @return array Конфигурация для подключения к БД, содержит:
	 *               - 'DB_HOST': хост базы данных,
	 *               - 'DB_NAME': имя базы данных,
	 *               - 'DB_USER': пользователь базы данных,
	 *               - 'DB_PASSWORD': пароль пользователя БД,
	 *               - 'DB_CHARSET': кодировка соединения с БД.
	 */
	public static function getDbConfig(): array
	{
		return [
			'DB_HOST' => 		$_ENV['MYSQL_HOST'],
			'DB_NAME' => 		$_ENV['MYSQL_DATABASE'],
			'DB_USER' => 		$_ENV['MYSQL_USER'],
			'DB_PASSWORD' => 	$_ENV['MYSQL_PASSWORD'],
			'DB_CHARSET' => 	'utf8mb4',
		];
	}
}