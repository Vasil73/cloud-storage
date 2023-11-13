<?php

namespace Controllers;

    use Exception;
    use InvalidArgumentException;
    use PDO;
    use PDOException;

    class AuthController
    {

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