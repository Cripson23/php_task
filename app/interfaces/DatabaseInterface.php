<?php

namespace app\interfaces;

use PDO;

/**
 * Интерфейс для подключения к базе данных.
 *
 * @package app\interfaces
 */
interface DatabaseInterface {
    /**
     * Получить подключение к базе данных.
     *
     * @return PDO
     */
    public function getConnection(): PDO;
}