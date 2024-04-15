<?php

namespace app\general;

use PDO;
use PDOException;
use app\interfaces\DatabaseInterface;

class Database implements DatabaseInterface {
	/**
	 * Статическая переменная для хранения единственного экземпляра класса.
	 *
	 * @var Database|null $instance Экземпляр Database
	 */
	private static ?Database $instance = null;

	/**
	 * Объект соединения с базой данных.
	 *
	 * @var PDO $conn Соединение с базой данных.
	 */
	private PDO $conn;

	/**
	 * Конструктор класса Database.
	 * Инициализирует соединение с базой данных на основе параметров из Config.
	 *
	 * @throws PDOException Если соединение с БД не может быть установлено.
	 */
	private function __construct() {
		try {
			$dbConfig = Config::getDbConfig();
			$dsn = "mysql:host=" . $dbConfig['DB_HOST'] . ";dbname=" . $dbConfig['DB_NAME'] . ";charset=" . $dbConfig['DB_CHARSET'];
			$this->conn = new PDO($dsn, $dbConfig['DB_USER'], $dbConfig['DB_PASSWORD']);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			$errorMessage = "Ошибка подключения к базе данных: " . $e->getMessage();
			Logger::logEchoError($errorMessage);
			return null;
		}
	}

	/**
	 * Возвращает единственный экземпляр класса Database.
	 * Создаёт новый экземпляр, если он ещё не существует.
	 *
	 * @return Database Экземпляр Database
	 */
	public static function getInstance(): Database {
		if (self::$instance === null) {
			self::$instance = new Database();
		}

		return self::$instance;
	}

	/**
	 * Возвращает объект соединения PDO.
	 *
	 * @return PDO Возвращает объект соединения с базой данных или null в случае ошибки.
	 */
	public function getConnection(): PDO {
		return $this->conn;
	}

	/**
	 * Предотвращает клонирование объекта.
	 */
	public function __clone() {
	}

	/**
	 * Предотвращает десериализацию объекта.
	 */
	public function __wakeup() {
	}
}