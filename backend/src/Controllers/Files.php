<?php

namespace App\Controllers;

use App\Connect\Database;
use App\Handler\TableValidator;
use Exception;
use PDO;
use PDOException;

    class Files
    {
        public PDO $pdo;
        private string $table_name;

        /**
         * @throws Exception
         */
        public function __construct(Database $database, string $table_name)
        {
            $this->table_name = $table_name;
            $this->pdo = $database->getPdo ();
        }

        /**
         * @throws Exception
         */
        public function fileList(): bool|string
        {
            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }

            try {
                $stmt = $this->pdo->prepare ( "SELECT id, name FROM files" );
                $stmt->execute ();
                $files = $stmt->fetchAll ();

                return json_encode ( $files );
            } catch (PDOException $e) {
                error_log ( $e->getMessage () );
                return 'Произошла ошибка при попытке получить файлы.: ' . $e->getMessage ();
            } catch (Exception $e) {
                error_log ( $e->getMessage () );
                return 'произошла непредвиденная ошибка: ' . $e->getMessage ();
            }
        }

        /**
         * @throws Exception
         */
        public function getFile($id): string
        {
            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }

            try {
                $stmt = $this->pdo->prepare ( "SELECT id, name, size FROM 
                     {$this->table_name} WHERE id = :id" );
                $stmt->bindValue ( ':id', $id );
                $stmt->execute ();
                $file = $stmt->fetch ( PDO::FETCH_ASSOC );

                if ($file === false) {
                    return json_encode ( ['error' => 'Файл с таким ID не найден'] );
                }

                return json_encode ( $file );
            } catch (PDOException $e) {
                error_log ( $e->getMessage () );

                return json_encode ( ['error' => 'Произошла ошибка при выполнении запроса'] );
            }
        }

        /**
         * @throws Exception
         */
        public function addFile($data): bool|string
        {
            $name = trim ( $data[ "name" ] );
            $size = intval ( $data[ "size" ] );
            if ($name == '' || $size <= 0) {
                // Если данные некорректны, возвращаем ошибку
                return "Invalid data provided";
            }

            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }

            $stmt = $this->pdo->prepare ( "INSERT INTO files
                (name, size) VALUES (:name, :size)" );
            $stmt->bindValue ( ':name', $name );
            $stmt->bindValue ( ':size', $size );
            $stmt->execute ();

            $id = $this->pdo->lastInsertId ();

            $file = ["id" => $id, "name" => $name, "size" => $size];

            return json_encode ( $file );
        }

        /**
         * @throws Exception
         */
        public function renameFile($id, $newName): bool|string
        {
            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }

            if (array_key_exists ('newName', $newName)) {
                $newName_value = $newName['newName'];
            }

            $stmt = $this->pdo->prepare ( "UPDATE {$this->table_name}
                            SET name = :newName WHERE id = :id" );
            $stmt->bindValue ( ':newName', $newName_value );
            $stmt->bindValue ( ':id', $id );
            $stmt->execute ();

            return json_encode ( ["id" => $id, "name" => $newName_value] );
        }

        /**
         * @throws Exception
         */
        public function removeFile($id): bool|string
        {
            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }

            $stmt = $this->pdo->prepare ( "DELETE FROM {$this->table_name} WHERE id = :id" );
            $stmt->bindValue ( ':id', $id );
            $stmt->execute ();

            return json_encode ( ["message" => "Файл успешно удален"] );
        }

    }
