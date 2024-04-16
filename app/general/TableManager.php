<?php

namespace app\general;

use app\interfaces\DatabaseInterface;
use app\interfaces\TableManagerInterface;

/**
 * Класс TableManager реализует методы для работы с базой данных.
 *
 * @package app\general
 */
class TableManager implements TableManagerInterface
{
    private DatabaseInterface $db;

    /**
     * Конструктор класса TableManager.
     *
     * @param DatabaseInterface $db Инстанс подключения к базе данных.
     */
    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    /**
     * Проверяет наличие таблицы в базе данных.
     *
     * @param string $tableName Название таблицы для проверки.
     * @return bool Возвращает true, если таблица существует, иначе false.
     */
    public function tableExists(string $tableName): bool {
        try {
            $result = $this->db->getConnection()->query("SELECT 1 FROM `$tableName` LIMIT 1");
            return $result !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Создаёт таблицу с заданными параметрами.
     *
     * @param string $tableName Название таблицы.
     * @param array $schema Массив строк, определяющих колонки и их типы.
     * @return void
     */
    public function createTable(string $tableName, array $schema): void
    {
        $columns = implode(', ', $schema);
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` ($columns) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        try {
            $this->db->getConnection()->exec($sql);
        } catch (\Exception $e) {
            Logger::logEchoMessage("Ошибка при создании таблицы '$tableName': " . $e->getMessage());
        }
    }
}