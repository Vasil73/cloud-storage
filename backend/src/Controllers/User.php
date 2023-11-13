<?php

namespace App\Controllers;

use App\Connect\Database;
use Exception;
use PDO;
use PDOException;
use InvalidArgumentException;

    class User
    {
        public PDO $pdo;

        public function __construct(Database $database)
        {
            $this->pdo = $database->getPdo();
        }

        public function getUsers(): string
        {

            $json_result = "{}";
            try {
                $query = "SELECT * FROM users";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $json_result = json_encode($result, JSON_THROW_ON_ERROR);
            } catch (\PDOException $e) {
                error_log('PDOException - ' . $e->getMessage(), 0);
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            } catch (\JsonException $e) {
                error_log('JsonException - ' . $e->getMessage(), 0);
            }

            header('Content-Type: application/json');
            return $json_result;
        }

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
                return json_encode(["status" => "error", "message" => "No results found for user ID: $userId"]);
            }

            header('Content-Type: application/json');
            return json_encode($result);
        }

        /**
         * @throws Exception
         */
        public function searchByEmail($email)
        {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if($email === false) {
                throw new InvalidArgumentException('Неверный адрес электронной почты');
            }

            try {
                $stmt = $this->pdo->prepare ( "SELECT * FROM users WHERE email= :email" );
                $stmt->bindValue () ( ':email', $email );
                $stmt->execute ();

                // return ? $user : null;

                return $stmt->fetch ( PDO::FETCH_ASSOC );

            } catch (PDOException $e) {
                error_log('ошибка базы данных: ' . $e->getMessage ());
                throw $e;
            }
        }

        // Обновление данных пользователя
        public function updateUser($id, $data)
        {
            try {
                $name = htmlspecialchars($data['name']);
                $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
                $age = filter_var($data['age'], FILTER_VALIDATE_INT);
                $gender = in_array($data['gender'], ['male', 'female']) ? $data['gender'] : null;

                if(!$email) {
                    throw new Exception('Неправильный формат email');
                }

                if(!$age) {
                    throw new Exception('Неправильный формат возраста');
                }

                if(!$gender) {
                    throw new Exception('Неправильный формат пола');
                }

                $query = $this->pdo->prepare("UPDATE users SET name = :name, email = :email, age = :age, 
                     gender = :gender WHERE id = :id");
                $query->bindValue(":id", $id, PDO::PARAM_INT);
                $query->bindValue(":name", $name, PDO::PARAM_STR);
                $query->bindValue(":email", $email, PDO::PARAM_STR);
                $query->bindValue(":age", $age, PDO::PARAM_INT);
                $query->bindValue(":gender", $gender, PDO::PARAM_STR);
                $query->execute();

                return true;
            } catch (Exception $e) {
                return "Ошибка обновления данных пользователя: " . $e->getMessage();
            }
        }

        public function deleteUser($id)
        {
            $query = "DELETE FROM users WHERE id = :Id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':Id', $id, PDO::PARAM_INT);
            $stmt->execute([':Id' => $id]);
        }

        // Регистрация ползователя
        public function register($email, $name, $password, $role, int $age = null, $gender = null): bool
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Неверный формат email");
            }
            if (strlen($password) < 6) {
                throw new InvalidArgumentException("Пароль должен быть не менее 6 символов");
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("INSERT INTO users (email, name, password, role, age, gender)
                                        VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $name, $hashedPassword, $role, $age, $gender]);

            return $stmt->rowCount() > 0;
        }

        // Вход в систему пользователем
        /**
         * @throws Exception
         */
        public function login(array $data): array
        {
            if(!isset($data['email']) || !isset($data['password'])) {
                throw new InvalidArgumentException("Email или пароль отсутствуют");
            }

            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Неверный формат email");
            }

            $password = $data['password'];

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && password_verify($password, $result["password"])) {
                $token = bin2hex(random_bytes(16));

                $stmt = $this->pdo->prepare("UPDATE users SET token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':id', $result["id"]);
                $stmt->execute();

                unset($result["password"]);

                $_SESSION['token'] = $token;

                return empty($result) ? ['error' => "Неверный логин или пароль"] : $result;

            }

            return ['error' => "Неверный логин или пароль"];

        }

        // Сброс пароля пользователя
        public function logout($id)
        {
            $stmt = $this->pdo->prepare('UPDATE users SET token = NULL WHERE id = :id');
            //  $stmt->execute([':token' => $userToken]);
            $stmt->execute([':id' => $id]);

            if (isset($_COOKIE['token'])) {
                setcookie('token', '', time() - 3600, '/', '', false, true);
            }

            return json_encode(['message' => 'Вышел из системы успешно']);
        }


        public function resetPassword(string $email): bool
        {
            try {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException('Неверный формат электронной почты');
                }

                // Check if user exists first
                $query = "SELECT * FROM users WHERE email = :email";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() === 0) {
                    throw new Exception('Ни один пользователь не найден с таким адресом электронной почты');
                }

                $resetToken = bin2hex(random_bytes(32));

                $query = "UPDATE users SET reset_token = :resetToken, reset_token_expires = NOW() + INTERVAL 1 
                         HOUR WHERE email = :email";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':resetToken', $resetToken, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $resetLink = "https://example.com/reset_password.php?token=$resetToken";
                    $to = $email;
                    $subject = "Сброс пароля";
                    $message = "Чтобы сбросить пароль, нажмите следующую ссылку: $resetLink";
                    $headers = "От: vasil@example.com";
                    if (mail($to, $subject, $message, $headers)) {
                        return true;
                    }
                }
            } catch (PDOException $e) {
                error_log('Ошибка сброса пароля: ' . $e->getMessage());
            } catch (Exception $exception) {
                error_log('Ошибка сброса пароля: ' . $exception->getMessage());
            }
            return false;
        }
    }