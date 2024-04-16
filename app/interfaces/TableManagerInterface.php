<?php

namespace app\interfaces;

/**
 * Интерфейс для классов реализующих функции по работе с таблицами
 *
 * @package app\interfaces
 */
interface TableManagerInterface {
    public function tableExists(string $tableName): bool;
    public function createTable(string $tableName, array $schema);
}