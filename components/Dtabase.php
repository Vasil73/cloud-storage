<?php

namespace components;

use Exception;
use InvalidArgumentException;
use PDO;

    class Dtabase
    {
        private static ?Dtabase $instance = null;
        private PDO $connection;

        // Используем private конструктор
        private function __construct(array $config)
        {
            $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'];
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        }

        // Запрещаем клонирование
        private function __clone()
        {

        }

        // Запрещаем десериализацию

        /**
         * @throws Exception
         */
        public function __wakeup()
        {
            throw new Exception("Cannot unserialize a singleton.");
        }

        // Получение экземпляра Db
        public static function getInstance(array $config = null): ?Dtabase
        {
            if (self::$instance === null) {
                if ($config === null) {
                    throw new InvalidArgumentException("Initial configuration is required for Db class.");
                }
                self::$instance = new self($config);
            }
            return self::$instance;
        }

        // Получение PDO соединения
        public function getConnection()
        {
            return $this->connection;
        }

        // Прочие методы...

        public function findBy($table, $criteria)
        {
            $conditions = [];
            $values = [];
            foreach ($criteria as $field => $value) {
                $conditions[] = "$field = ?";
                $values[] = $value;
            }
            $conditions = implode(' AND ', $conditions);

            $sql = "SELECT * FROM {$table} WHERE {$conditions}";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($values);

            return $stmt->fetchAll();
        }

        public function findOneBy($table, $criteria)
        {
            $results = $this->findBy($table, $criteria);
            if (count($results) > 0) {
                return $results[0];
            }
            return null;
        }

        public function findAll($table)
        {
            $sql = "SELECT * FROM {$table}";
            $stmt = $this->connection->query($sql);

            return $stmt->fetchAll();
        }

        public function find($table, $id)
        {
            $sql = "SELECT * FROM {$table} WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$id]);

            $result = $stmt->fetch();
            return $result ? $result : null;
        }
    }

