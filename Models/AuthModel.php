<?php

namespace Models;


    use Core\Database;
    use Core\Response;
    use Exception;
    use InvalidArgumentException;
    use PDO;
    use PDOException;

    class AuthModel
    {
        private PDO $pdo;

        public function __construct()
        {
            $this->pdo = Database::getInstance ();
        }


        public function register(string $name, string $email, string $password, string $role): bool
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("Неверный формат email");
            }
            if (strlen($password) < 6) {
                throw new \InvalidArgumentException("Пароль должен быть не менее 6 символов");
            }
            if ($this->userExists($email)) {
                Response::sendJsonResponse ( ["error" => 'Пользователь с таким email уже есть'] );
                return false;
            }

            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);

            return $stmt->rowCount() > 0;
        }

        public function authenticate($email)
        {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function setToken($id, $token): void
        {
            $stmt = $this->pdo->prepare ( "UPDATE users SET token = :token WHERE id = :id" );
            $stmt->bindParam ( ':token', $token );
            $stmt->bindParam ( ':id', $id );
            $stmt->execute ();
        }

        public function logout($id): void
        {
            $stmt = $this->pdo->prepare ( 'UPDATE users SET token = NULL WHERE id = :id' );
            $stmt->execute ( [':id' => $id['id']] ); //
        }

        public function resetPassword(string $email): bool
        {
            try {
                if (!filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
                    throw new InvalidArgumentException( 'Неверный формат электронной почты' );
                }

                $query = "SELECT * FROM users WHERE email = :email";
                $stmt = $this->pdo->prepare ( $query );
                $stmt->bindParam ( ':email', $email, PDO::PARAM_STR );
                $stmt->execute ();
                if ($stmt->rowCount () === 0) {
                    throw new Exception( 'Ни один пользователь не найден с таким адресом электронной почты' );
                }

                $resetToken = bin2hex ( random_bytes ( 32 ) );

                $query = "UPDATE users SET reset_token = :resetToken, reset_token_expires = NOW() + INTERVAL 1
                         HOUR WHERE email = :email";
                $stmt = $this->pdo->prepare ( $query );
                $stmt->bindParam ( ':resetToken', $resetToken, PDO::PARAM_STR );
                $stmt->bindParam ( ':email', $email, PDO::PARAM_STR );
                if ($stmt->execute ()) {
                    $resetLink = "https://example.com/reset_password.php?token=$resetToken";
                    $to = $email;
                    $subject = "Сброс пароля";
                    $message = "Чтобы сбросить пароль, нажмите следующую ссылку: $resetLink";
                    $headers = "От: example@example.com";
                    if (mail ( $to, $subject, $message, $headers )) {
                        return true;
                    }
                }
            } catch (PDOException $e) {
                error_log ( 'Ошибка сброса пароля: PDOException:' . $e->getMessage () );
            } catch (Exception $exception) {
                error_log ( 'Ошибка сброса пароля: Exception' . $exception->getMessage () );
            }

            return false;
        }

        public function userExists($email): bool
        {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        }
    }