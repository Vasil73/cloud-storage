<?php

namespace Handler;

    use Exception;


    class TableValidator
    {
        private string $table_name;

        public function __construct(string $table_name)
        {
            $this->table_name = $table_name;
        }

        /**
         * @throws Exception
         */
        private function validateTableName(): void
        {
            $allowedTables = ['files', 'folders', 'file_user', 'users','sessions'];

            if (!in_array($this->table_name, $allowedTables)) {
                throw new Exception("Invalid table name: {$this->table_name}");
            }
        }

        /**
         * @throws Exception
         */
        public function check(): void
        {
            $this->validateTableName();
        }
    }