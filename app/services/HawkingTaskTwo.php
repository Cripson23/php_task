<?php

namespace app\services;

use PDO;
use PDOException;
use app\general\Database;
use app\general\Logger;
use app\general\TableManager;
use app\interfaces\TableManagerInterface;

final class HawkingTaskTwo
{
    /**
     * Интерфейс для управления таблицами в базе данных.
     * Предоставляет методы для проверки существования таблиц и их создания.
     *
     * @var TableManagerInterface
     */
    private TableManagerInterface $tableManager;

    /**
     * Количество записей для обработки или вставки в таблицу.
     *
     * @var int
     */
    private int $recordCount;

    /**
     * Экземпляр подключения к базе данных.
     * Используется для выполнения операций с базой данных.
     *
     * @var Database
     */
    private Database $db;

    /**
     * Конструктор класса HawkingTaskTwo.
     * Получает экземпляр подключения к БД или инициализирует его.
     *
     * @throws PDOException Если соединение с БД не может быть установлено.
     */
    public function __construct(int $recordCount)
    {
        $this->db = Database::getInstance();
        $this->tableManager = new TableManager($this->db);
        $this->recordCount = $recordCount;
        $this->createTables();
        $this->populateData($this->recordCount);
    }

    /**
     * Выполняет SQL-запрос на выборку данных из таблиц data, link и info,
     * Улучшение: используется соединение таблиц через INNER JOIN.
     *
     * @return array|false Возвращает массив с данными или false в случае ошибки.
     */
    public function makeQuery(): array|false
    {
        $dbConnection = $this->db->getConnection();

        // Проверяем существование таблиц
        $requiredTables = ['data', 'link', 'info'];
        foreach ($requiredTables as $table) {
            if (!$this->tableManager->tableExists($table)) {
                $errorMessage = "Ошибка: таблица $table не существует.";
                Logger::logEchoError($errorMessage);
                return false;
            }
        }

        $sql = "
            SELECT *
            FROM data
            INNER JOIN link ON link.data_id = data.id
            INNER JOIN info ON link.info_id = info.id;
        ";

        try {
            $stmt = $dbConnection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $errorMessage = "Ошибка при выполнении запроса: " . $e->getMessage();
            Logger::logEchoError($errorMessage);
            return false;
        }
    }

    /**
     * Создаёт оптимизированные таблицы: info, data, link.
     * Улучшения:
     *  1) Для поля name уменьшен размер с 255 на 100 (предполагая, что 255 может быть избыточным для поля с данным названием).
     *  2) Изменен движок на InnoDb, более современный.
     *  3) Для таблицы link добавлены индексы и внешние ключи для обеспечения целостности данных и улучшения запросов с соединениями.
     *  4) Кодировка изменена на utf8mb4 для лучшей совместимости с различными языками и Unicode.
     *
     * @return void
     */
    private function createTables(): void
    {
        try {
            $this->tableManager->createTable('info', [
                "`id` INT AUTO_INCREMENT PRIMARY KEY",
                "`name` VARCHAR(100) DEFAULT NULL",
                "`desc` TEXT DEFAULT NULL"  // Использование обратных кавычек
            ]);
            $this->tableManager->createTable('data', [
                "`id` INT AUTO_INCREMENT PRIMARY KEY",
                "`date` DATE DEFAULT NULL",
                "`value` INT DEFAULT NULL"
            ]);
            $this->tableManager->createTable('link', [
                "`data_id` INT NOT NULL",
                "`info_id` INT NOT NULL",
                "FOREIGN KEY (`data_id`) REFERENCES `data`(`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "FOREIGN KEY (`info_id`) REFERENCES `info`(`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Ошибка при создании таблиц: ' . $e->getMessage();
            Logger::logEchoError($errorMessage);
        }
    }

    /**
     * Заполняет таблицы случайными данными.
     *
     * @param int $recordCount Количество записей для добавления
     * @return void
     */
    private function populateData(int $recordCount): void
    {
        if (!$this->tableManager->tableExists('data') || !$this->tableManager->tableExists('info')) {
            Logger::logEchoMessage("Одна из таблиц 'data' или 'info' не существует.");
            return;
        }

        $dbConnection = $this->db->getConnection();
        // Заполнение таблицы data
        $stmtData = $dbConnection->prepare("INSERT INTO data (`date`, `value`) VALUES (:date, :value)");
        for ($i = 0; $i < $recordCount; $i++) {
            $stmtData->execute([
                ':date' => date('Y-m-d', time() - rand(0, 86400 * 365)),
                ':value' => rand(0, 10000)
            ]);
        }

        // Заполнение таблицы info
        $stmtInfo = $dbConnection->prepare("INSERT INTO info (`name`, `desc`) VALUES (:name, :desc)");
        for ($i = 0; $i < $recordCount; $i++) {
            $stmtInfo->execute([
                ':name' => 'Имя' . rand(1, 10000),
                ':desc' => 'Описание' . rand(1, 10000)
            ]);
        }

        // Связывание данных в таблице link с проверкой на уникальность
        $stmtLink = $dbConnection->prepare("INSERT INTO link (`data_id`, `info_id`) SELECT :data_id, :info_id WHERE NOT EXISTS (SELECT 1 FROM link WHERE data_id = :data_id AND info_id = :info_id)");
        for ($i = 1; $i <= $recordCount; $i++) {
            $stmtLink->execute([
                ':data_id' => $i,
                ':info_id' => $i
            ]);
        }

        Logger::logEchoMessage("Данные успешно добавлены в таблицы 'data', 'info' и 'link'.");
    }
}