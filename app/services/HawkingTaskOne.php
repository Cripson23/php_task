<?php

namespace app\services;

use PDO;
use PDOException;
use app\general\TableManager;
use app\interfaces\TableManagerInterface;
use app\general\Database;
use app\general\Logger;

final class HawkingTaskOne
{
    private TableManagerInterface $tableManager;
    private string $tableName;
    private int $recordCount;
    private Database $db;

	/**
	 * Конструктор класса HawkingTaskOne.
     * Получает экземпляр подключения к БД или инициализирует его.
	 * Вызывает выполнение операций по созданию таблицы и заполнению её случайными данными.
	 *
	 * @throws PDOException Если соединение с БД не может быть установлено.
	 */
	public function __construct(string $tableName, int $recordCount)
    {
        $this->db = Database::getInstance();
        $this->tableManager = new TableManager($this->db);
        $this->tableName = $tableName;
        $this->recordCount = $recordCount;
        $this->bros();
        $this->brothers($this->recordCount);
	}

	/**
	 * Выполняет запрос на поиск записей с результатом 'normal' или 'success'
	 *
	 * @return array|false Возвращает данные или индикатор неуспешного выполнения запроса
	 */
    public function production(): bool|array
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE result IN ('normal', 'success')";
        try {
            $stmt = Database::getInstance()->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            Logger::logEchoError("Ошибка при выборке данных: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Создаёт таблицы в рамках задачи 1
     *
     * @return void
     */
    private function bros(): void
    {
        $schema = [
            "id INT AUTO_INCREMENT PRIMARY KEY",
            "script_name VARCHAR(25) NOT NULL",
            "start_time INT",
            "end_time INT",
            "result ENUM('normal', 'illegal', 'failed', 'success') NOT NULL"
        ];

        $this->tableManager->createTable($this->tableName, $schema);
    }

	/**
	 * Заполняет таблицу случайными данными
	 *
	 * @param int $numRecords Количество записей для добавления
	 * @return void
	 */
	private function brothers(int $numRecords): void
	{
		try {
			$dbConnection = $this->db->getConnection();

            if (!$this->tableManager->tableExists($this->tableName)) {
                $errorMessage = "Таблица '" . $this->tableName . "' не существует для заполнения.";
                Logger::logEchoError($errorMessage);
                return;
            }

            $results = ['normal', 'illegal', 'failed', 'success'];

			$sql = "INSERT INTO `$this->tableName` (script_name, start_time, end_time, result) VALUES (:script_name, :start_time, :end_time, :result)";

			$stmt = $dbConnection->prepare($sql);

			for ($i = 0; $i < $numRecords; $i++) {
				$script_name = 'script_' . rand(0, 10000);
				$start_time = time() - rand(0, 10000);
				$end_time = $start_time + rand(0, 10000);
				$result = $results[array_rand($results)];

				$stmt->execute([
					':script_name' => $script_name,
					':start_time' => $start_time,
					':end_time' => $end_time,
					':result' => $result
				]);
			}

			$message = "Вставлено $numRecords записей в таблицу " . $this->tableName . ".";
			Logger::logEchoMessage($message);
		} catch (\Exception $e) {
			$errorMessage = "Ошибка при заполнении случайными данными таблицы: " . $e->getMessage();
			Logger::logEchoError($errorMessage);
		}
	}
}