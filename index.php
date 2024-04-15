<?php

use app\CommandHandler;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($argc > 1) {
	CommandHandler::handle($argv);
} else {
    echo "Необходимо передать один из параметров: task_1, task_2, task_3\n";
}