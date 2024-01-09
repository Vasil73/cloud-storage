<?php

namespace Core;

use Exception;
use PDO;
use PDOException;

    class Database
    {
        private static ?PDO $pdoInstance = null;

        private function __construct()
        {
            // Закрытый конструктор для Singleton.
        }

        public static function getInstance(): PDO
        {
            if (self::$pdoInstance === null) {
                $config = self::getConfig();
                $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';
                try {
                    self::$pdoInstance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                } catch (PDOException $e) {
                    die('Не удалось подключиться к базе данных: ' . $e->getMessage());
                }
            }

            return self::$pdoInstance;
        }

        private static function getConfig()
        {
            return include 'common/db.php';
        }

    }

