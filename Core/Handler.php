<?php

namespace Core;

use Exception;
use InvalidArgumentException;
use PDO;

    class Handler
    {
        public PDO $pdo;
        private string $table_name;

        /**
         * @param string $table_name
         */
        public function __construct(string $table_name)
        {
            $this->table_name = $table_name;
            $this->pdo = Database::getInstance ();
        }

        // Вспомогательный метод валидации данных пользователя


        /**
         * @throws Exception
         */
        public function validateUser($userData, string $userName)
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();
            if (empty($userData['name']) ||
                !filter_var($userData['email'], FILTER_VALIDATE_EMAIL) ||
                !filter_var($userData['age'], FILTER_VALIDATE_INT) ||
                !in_array($userData['gender'], ['male', 'female'])) {
                throw new InvalidArgumentException('Неверные данные пользователя.');
            }
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table_name} WHERE name = :name");
            $stmt->bindValue(':name', $userName);
            if(!$stmt->execute()) {
                throw new Exception("Ошибка проверки существования папки.");
            }

        }

        public function userExists($email): bool
        {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        }

        public function setToken($id, $token): void
        {
            $stmt = $this->pdo->prepare ( "UPDATE users SET token = :token WHERE id = :id" );
            $stmt->bindParam ( ':token', $token );
            $stmt->bindParam ( ':id', $id );
            $stmt->execute ();
        }


    }