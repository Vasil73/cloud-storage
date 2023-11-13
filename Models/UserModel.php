<?php

namespace Models;

    use components\Database;
    use Exception;
    use InvalidArgumentException;
    use PDO;
    use PDOException;

    // use http\Exception;

    class UserModel
    {
        private PDO $pdo;
        const JSON_CONTENT_TYPE = 'application/json';

        public function __construct()
        {
            $this->pdo = Database::getInstance ();
        }

        public function getUsers(): string
        {
            $json_result = "{}";
            try {
                $stmt = $this->pdo->prepare ( "SELECT * FROM users" );
                $stmt->execute ();
                $result = $stmt->fetchAll ( PDO::FETCH_ASSOC );
                $json_result = json_encode ( $result, JSON_THROW_ON_ERROR );
            } catch (\PDOException $e) {
                error_log ( 'PDOException - ' . $e->getMessage (), 0 );
                throw new \PDOException( $e->getMessage (), (int)$e->getCode () );
            } catch (\JsonException $e) {
                error_log ( 'JsonException - ' . $e->getMessage (), 0 );
            }

            header ( 'Content-Type: ' . self::JSON_CONTENT_TYPE );
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
            $email = filter_var ( $email, FILTER_VALIDATE_EMAIL );
            if ($email === false) {
                throw new InvalidArgumentException( 'Invalid email address' );
            }

            try {
                $stmt = $this->pdo->prepare ( "SELECT * FROM users WHERE email= :email" );
                $stmt->bindValue ( ':email', $email );
                $stmt->execute ();

                return $stmt->fetch ( PDO::FETCH_ASSOC );

            } catch (PDOException $e) {
                error_log ( 'Database error: ' . $e->getMessage () );
                throw $e;
            }
        }

        public function updateUser($id, $data)
        {
            try {
                $name = htmlspecialchars ( $data[ 'name' ] );
                $email = filter_var ( $data[ 'email' ], FILTER_VALIDATE_EMAIL );
                $age = filter_var ( $data[ 'age' ], FILTER_VALIDATE_INT );
                $gender = in_array ( $data[ 'gender' ], ['male', 'female'] ) ? $data[ 'gender' ] : null;

                if (!$email) {
                    throw new Exception( 'Invalid email format' );
                }

                if (!$age) {
                    throw new Exception( 'Invalid age format' );
                }

                if (!$gender) {
                    throw new Exception( 'Invalid gender format' );
                }

                $query = $this->pdo->prepare ( "UPDATE users SET name = :name, email = :email, age = :age, 
                 gender = :gender WHERE id = :id" );
                $query->bindValue ( ":id", $id, PDO::PARAM_INT );
                $query->bindValue ( ":name", $name, PDO::PARAM_STR );
                $query->bindValue ( ":email", $email, PDO::PARAM_STR );
                $query->bindValue ( ":age", $age, PDO::PARAM_INT );
                $query->bindValue ( ":gender", $gender, PDO::PARAM_STR );
                $query->execute ();

                return true;
            } catch (Exception $e) {
                return "Error updating user data: " . $e->getMessage ();
            }
        }

        public function deleteUser($id)
        {
            $query = "DELETE FROM users WHERE id = :Id";
            $stmt = $this->pdo->prepare ( $query );
            $stmt->bindParam ( ':Id', $id, PDO::PARAM_INT );
            $stmt->execute ( [':Id' => $id] );
        }
    }
