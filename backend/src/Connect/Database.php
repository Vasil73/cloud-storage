<?php

namespace App\Connect;

use PDO;
use PDOException;

    class Database
    {
        private PDO $pdo;

        public function __construct()
        {
            $host = 'localhost: 3306';
            $dbname = 'cloud_storage';
            $username = 'root';
            $password = '';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_FOUND_ROWS => true,
            ];

            try {
                $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
            } catch (PDOException $e) {
                throw new PDOException('Нет подключения к базе данных' . $e->getMessage ());
            }
        }

        public function getPdo(): PDO
        {
            return $this->pdo;
        }
    }