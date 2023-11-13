<?php

namespace App\Controllers;

use App\Connect\Database;
use App\Handler\TableValidator;
use Exception;
use PDO;

    class FileAccess
        {
            public PDO $pdo;
            private string $table_name;

            /**
             * @param Database $database
             * @param $table_name
             */
            public function __construct(Database $database, $table_name)
            {
                $this->table_name = $table_name;
                $this->pdo = $database->getPdo ();
            }

            /**
             * @throws Exception
             */
            public function addSharedUsers($file_id, $user_id): bool|\PDOStatement
            {
                try {
                    $validator = new TableValidator( $this->table_name );
                    $validator->check ();
                } catch (Exception $e) {
                    return 'Произошла ошибка подключения: ' . $e->getMessage ();
                }

                $stmt = $this->pdo->prepare ( "INSERT INTO " . $this->table_name . "
                         (file_id, user_id) VALUES (:file_id, :user_id)" );
                $stmt->bindParam ( ':file_id', $file_id, PDO::PARAM_INT );
                $stmt->bindParam ( ':user_id', $user_id, PDO::PARAM_INT );
                if ($stmt->execute ()) {
                    return json_encode ( ["message" => "Пользователь успешно добавлен в общий доступ"] );
                }
                return "Не удалось добавить пользователя для общего доступа.";
            }


            /**
             * @throws Exception
             */
            public function getSharedUsers($fileId): bool|string
            {
                try {
                    $validator = new TableValidator( $this->table_name );
                    $validator->check ();
                } catch (Exception $e) {
                    return 'Произошла ошибка подключения: ' . $e->getMessage ();
                }

                $stmt = $this->pdo->prepare ( "SELECT u.* FROM users u
                        INNER JOIN " . $this->table_name . " fu ON fu.user_id = u.id
                        WHERE fu.file_id = :file_id" );
                $stmt->bindParam ( ':file_id', $fileId, PDO::PARAM_INT );
                $stmt->execute ();
                $users = $stmt->fetchAll ( PDO::FETCH_ASSOC );

                if (!empty( $users )) {
                    return json_encode ( $users );
                }

                return 'Для этого файла не найдено общих пользователей';
            }


            /**
             * @throws Exception
             */
            public function removeSharedUser($file_id, $user_id): bool|\PDOStatement
            {
                try {
                    $validator = new TableValidator( $this->table_name );
                    $validator->check ();
                } catch (Exception $e) {
                    return 'Произошла ошибка подключения: ' . $e->getMessage ();
                }

                $stmt = $this->pdo->prepare ( "DELETE FROM {$this->table_name} 
                        WHERE file_id = :file_id AND id = :user_id" );
                $stmt->bindParam ( ':file_id', $file_id );
                $stmt->bindParam ( ':user_id', $user_id );
                $stmt->execute ( [':file_id' => $file_id, 'user_id' => $user_id] );

                return $stmt;
            }
        }