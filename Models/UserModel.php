<?php

namespace Models;

    use Core\Database;
    use Core\Response;
    use Core\TableValidator;
    use Exception;
    use InvalidArgumentException;
    use JsonException;
    use PDO;
    use PDOException;

    class UserModel
    {
        protected PDO $pdo;
        private mixed $table_name;

        public function __construct(string $table_name)
        {
            $this->table_name = $table_name;
            $this->pdo = Database::getInstance ();
        }

        /**
         * @throws Exception
         */
        public function getUsers(): ?array
        {
            $validator = new TableValidator($this->table_name);
               $validator->check();

            $stmt = $this->pdo->prepare ( "SELECT * FROM `{$this->table_name}`" );
            $stmt->execute ();

            $result = $stmt->fetchAll ( PDO::FETCH_ASSOC );
            if (!$result) {
                return null;
            }
            return $result;
        }

        /**
         * @throws Exception
         */
        public function getUserById($userId)
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $userId = intval($userId);
            if ($userId <= 0) {
                throw new InvalidArgumentException("Invalid user ID.");
            }

            $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :userId");
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return null;
            }

            return $result;
        }

        /**
         * @throws Exception
         */
        public function updateUser($id, $data): bool
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

                $name = htmlspecialchars($data['name']);
                $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
                $age = filter_var($data['age'], FILTER_VALIDATE_INT);
                $gender = in_array($data['gender'], ['male', 'female']) ? $data['gender'] : null;

                $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = :name, email = :email, age = :age, 
                     gender = :gender WHERE id = :id");
                $query->bindParam(":id", $id, PDO::PARAM_INT);
                $query->bindParam(":name", $name, PDO::PARAM_STR);
                $query->bindParam(":email", $email, PDO::PARAM_STR);
                $query->bindParam(":age", $age, PDO::PARAM_INT);
                $query->bindParam(":gender", $gender, PDO::PARAM_STR);
                $query->execute();

                return true;
        }

        /**
         * @throws Exception
         */
        public function searchByEmail($email)
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if($email === false) {
                throw new InvalidArgumentException('Неверный адрес электронной почты');
            }

            try {
                $stmt = $this->pdo->prepare ( "SELECT * FROM " . $this->table_name . " WHERE email= :email" );
                $stmt->bindValue  ( ':email', $email );
                $stmt->execute ();

                return $stmt->fetch ( PDO::FETCH_ASSOC );

            } catch (PDOException $e) {
                error_log('ошибка базы данных: ' . $e->getMessage ());
                throw $e;
            }
        }

        /**
         * @throws Exception
         */
        public function deleteUserById($id): bool
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $id = intval ($id);
            if ($id <= 0) {
                return false;
            }

            $stmt = $this->pdo->prepare('DELETE FROM " . $this->table_name . " WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }

    }
