<?php

namespace Models;

use Core\Database;
use Core\TableValidator;
use Exception;
use PDO;

    abstract class BaseModel
    {
        protected PDO $pdo;
        protected string $table_name;

        /**
         * @throws Exception
         */
        public function __construct(string $table_name)
        {
            $this->table_name = $table_name;
            $this->pdo = Database::getInstance();
            $this->checkTable();
        }

        /**
         * @throws Exception
         */
        protected function checkTable(): void
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();
        }
    }