<?php

namespace Core;

    use Exception;


    class TableValidator
    {
        private string $table_name;
        private array $allowedTables;

        public function __construct(string $table_name)
        {
            $this->table_name = $table_name;
            $this->allowedTables = include 'common/allowed_tables.php';
        }

        /**
         * @throws Exception
         */
        private function validateTableName()
        {

            if (!in_array($this->table_name, $this->allowedTables)) {
                throw new Exception("Неверное имя таблицы: {$this->table_name}");
            }
        }

        /**
         * @throws Exception
         */
        public function check()
        {
            $this->validateTableName();
        }
    }