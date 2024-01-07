<?php

namespace Models;

    use Core\Database;
    use Core\TableValidator;
    use Exception;
    use PDO;

    class FileAccessModel
    {
        public PDO $pdo;
        private string $table_name;

        public function __construct($table_name)
        {
            $this->table_name = $table_name;
            $this->pdo = Database::getInstance ();
        }

        /**
         * @throws Exception
         */
        public function addSharedUser($file_id, $user_id): bool
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();
            $stmt = $this->pdo->prepare ( "INSERT INTO  " . $this->table_name . "
                    (file_id, user_id) VALUES (:file_id, :user_id)" );
            $stmt->bindParam ( ':file_id', $file_id, PDO::PARAM_INT );
            $stmt->bindParam ( ':user_id', $user_id, PDO::PARAM_INT );
            return $stmt->execute ();
        }

        public function getSharedUsers($fileId): array
        {
            $stmt = $this->pdo->prepare ( "SELECT u.* FROM users u
                INNER JOIN " . $this->table_name . " fu ON fu.user_id = u.id
                WHERE fu.file_id = :file_id" );
            $stmt->bindParam ( ':file_id', $fileId, PDO::PARAM_INT );
            $stmt->execute ();
            return $stmt->fetchAll ( PDO::FETCH_ASSOC );
        }

        public function removeSharedUser($file_id, $user_id): bool
        {
            $stmt = $this->pdo->prepare ( "DELETE FROM " . $this->table_name . "
                WHERE file_id = :file_id AND id = :user_id" );
            $stmt->bindParam ( ':file_id', $file_id );
            $stmt->bindParam ( ':user_id', $user_id );
            $stmt->execute ( [':file_id' => $file_id, 'user_id' => $user_id] );

            return ($stmt->rowCount() > 0);
        }
    }