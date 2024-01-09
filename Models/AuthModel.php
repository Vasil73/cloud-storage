<?php

namespace Models;

use Core\TableValidator;
use Exception;
use InvalidArgumentException;
use PDO;
use PDOException;

    class AuthModel extends BaseModel
    {
        /**
         * @param string $table_name
         * @throws Exception
         */
        public function __construct(string $table_name)
        {
            parent::__construct($table_name);
        }

        /**
         * @throws Exception
         */
        public function register(string $name, string $email, string $password, string $role)
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("Неверный формат email");
            }
            if (strlen($password) < 6) {
                throw new \InvalidArgumentException("Пароль должен быть не менее 6 символов");
            }
            if ($this->userExists($email)) {
                throw new \InvalidArgumentException(  'Пользователь с таким email уже есть' );
            }

            $stmt = $this->pdo->prepare("INSERT INTO $this->table_name (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);

            return $stmt->rowCount() > 0;
        }

        /**
         * @throws Exception
         */
        public function authenticate($email)
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $stmt = $this->pdo->prepare("SELECT * FROM $this->table_name WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        /**
         * @throws Exception
         */
        public function setToken($id, $token): void
        {
            $stmt = $this->pdo->prepare ( "UPDATE $this->table_name SET token = :token WHERE id = :id" );
            $stmt->bindParam ( ':token', $token );
            $stmt->bindParam ( ':id', $id );
            $stmt->execute ();
        }

        /**
         * @throws Exception
         */
        public function logout($id): void
        {
            $stmt = $this->pdo->prepare ( "UPDATE $this->table_name SET token = NULL WHERE id = :id" );
            $stmt->execute ( [':id' => $id['id']] ); //
        }

        /**
         * @throws Exception
         */
        public function resetPassword(string $email): bool
        {
            try {
                if (!filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
                    throw new InvalidArgumentException( 'Неверный формат электронной почты' );
                }

                $query = "SELECT * FROM $this->table_name WHERE email = :email";
                $stmt = $this->pdo->prepare ( $query );
                $stmt->bindParam ( ':email', $email, PDO::PARAM_STR );
                $stmt->execute ();
                if ($stmt->rowCount () === 0) {
                    throw new Exception( 'Ни один пользователь не найден с таким адресом электронной почты' );
                }

                $resetToken = bin2hex ( random_bytes ( 32 ) );

                $query = "UPDATE $this->table_name SET reset_token = :resetToken, reset_token_expires = NOW() + INTERVAL 1
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

        /**
         * @throws Exception
         */
        public function userExists($email): bool
        {
            $stmt = $this->pdo->prepare("SELECT id FROM $this->table_name WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        }
    }