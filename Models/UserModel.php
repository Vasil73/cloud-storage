<?php

namespace Models;

    use Core\Database;
    use Core\Response;
    use Exception;
    use InvalidArgumentException;
    use JsonException;
    use PDO;
    use PDOException;

    class UserModel
    {
        protected PDO $pdo;

        public function __construct()
        {
            $this->pdo = Database::getInstance ();
        }

        public function addUser($userData): bool
        {
            $name = htmlspecialchars($userData['name']);
            $email = filter_var($userData['email'], FILTER_VALIDATE_EMAIL);
            $age = filter_var($userData['age'], FILTER_VALIDATE_INT);
            $gender = in_array($userData['gender'], ['male', 'female']) ? $userData['gender'] : null;

            if (!$email || !$age || !$gender) {

                return false;
            }

            $query = $this->pdo->prepare('INSERT INTO users (name, email, age, gender)
            VALUES (:name, :email, :age, :gender)');
            $query->bindValue(':name', $name, PDO::PARAM_STR);
            $query->bindValue(':email', $email, PDO::PARAM_STR);
            $query->bindValue(':age', $age, PDO::PARAM_INT);
            $query->bindValue(':gender', $gender, PDO::PARAM_STR);

            return $query->execute();
        }

        public function updateUser($id, $data): bool
        {
                $name = htmlspecialchars($data['name']);
                $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
                $age = filter_var($data['age'], FILTER_VALIDATE_INT);
                $gender = in_array($data['gender'], ['male', 'female']) ? $data['gender'] : null;

                $query = $this->pdo->prepare("UPDATE users SET name = :name, email = :email, age = :age, 
                     gender = :gender WHERE id = :id");
                $query->bindParam(":id", $id, PDO::PARAM_INT);
                $query->bindParam(":name", $name, PDO::PARAM_STR);
                $query->bindParam(":email", $email, PDO::PARAM_STR);
                $query->bindParam(":age", $age, PDO::PARAM_INT);
                $query->bindParam(":gender", $gender, PDO::PARAM_STR);
                $query->execute();

                return true;
        }

        public function getUsers(): array
        {
            $stmt = $this->pdo->prepare ( "SELECT * FROM users" );
            $stmt->execute ();

            return $stmt->fetchAll ( PDO::FETCH_ASSOC );
        }

        /**
         * @throws JsonException
         */
        public function getUserById($userId)
        {
            $userId = intval ($userId);

            if ($userId <= 0) {
                return json_encode(["status" => "error", "message" => "Invalid user ID."]);
            }

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :userId");
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Проверка на то, что результат не является FALSE
            if (!$result) {
                return json_encode(["status" => "error", "message" => "User not found."]);
            }
            else {
                return json_encode($result, JSON_THROW_ON_ERROR);
            }
        }

        public function searchByEmail($email)
        {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if($email === false) {
                throw new InvalidArgumentException('Неверный адрес электронной почты');
            }

            try {
                $stmt = $this->pdo->prepare ( "SELECT * FROM users WHERE email= :email" );
                $stmt->bindValue  ( ':email', $email );
                $stmt->execute ();

                return $stmt->fetch ( PDO::FETCH_ASSOC );

            } catch (PDOException $e) {
                error_log('ошибка базы данных: ' . $e->getMessage ());
                throw $e;
            }
        }

        public function deleteUserById($id): bool
        {
            $id = intval ($id);
            if ($id <= 0) {
                return false;
            }

            $query = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
            $query->bindValue(':id', $id, PDO::PARAM_INT);

            return $query->execute();
        }

    }
