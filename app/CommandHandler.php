<?php

namespace app;

use app\services\HawkingTaskOne;
use app\services\HawkingTaskThree;
use app\services\HawkingTaskTwo;

class CommandHandler
{
    /**
     * Список доступных команд
     *
     * @var array
     */
    private const COMMANDS = [
        'TASK_1' => 'task_1',
        'TASK_2' => 'task_2',
        'TASK_3' => 'task_3',
    ];

    /**
     * Название таблицы по умолчанию
     *
     * @var string
     */
    private const DEFAULT_TABLE_NAME = 'test';

    /**
     * Количество записей для вставки по умолчанию
     *
     * @var int
     */
    private const DEFAULT_RECORD_COUNT = 50;

    /**
     * Вызывает выполнение функционала в зависимости от переданного аргумента
     *
     * @param array $argv Массив аргументов командной строки
     * @return void
     */
    public static function handle(array $argv): void
    {
        $command = $argv[1];
        $params = self::parseParameters($argv);

        switch ($command) {
            case self::COMMANDS['TASK_1']:
                $table = $params['tableName'] ?? self::DEFAULT_TABLE_NAME;
                $recordCount = isset($params['recordCount']) &&
                    (intval($params['recordCount']) && is_numeric($params['recordCount']))
                        ? $params['recordCount']
                        : self::DEFAULT_RECORD_COUNT;

                $hawkingTaskOne = new HawkingTaskOne($table, $recordCount);
                $prodResult = $hawkingTaskOne->production();
                print_r($prodResult);
                break;
            case self::COMMANDS['TASK_2']:
                $recordCount = isset($params['recordCount']) &&
                (intval($params['recordCount']) && is_numeric($params['recordCount']))
                    ? $params['recordCount']
                    : self::DEFAULT_RECORD_COUNT;

                $hawkingTaskTwo = new HawkingTaskTwo($recordCount);
                $queryResult = $hawkingTaskTwo->makeQuery();
                print_r($queryResult);
                break;
            case self::COMMANDS['TASK_3']:
                HawkingTaskThree::listFilteredFiles();
                break;
            default:
                echo "Неизвестная команда.\n";
        }
    }

    /**
     * Определяет переданные в команду параметры
     *
     * @param array $argv Массив аргументов командной строки
     * @return array Массив с обработанными аргументами
     */
    private static function parseParameters(array $argv): array {
        $params = [];
        foreach ($argv as $arg) {
            if (preg_match('/^--(\w+)=(.*)$/', $arg, $matches)) {
                $params[$matches[1]] = $matches[2];
            }
        }
        return $params;
    }
}