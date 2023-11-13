<?php

namespace App\Controllers;


use App\Connect\Database;
use App\Handler\TableValidator;
use Exception;
use PDO;

    class FoldersFile
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
            public function addFolder($data): bool|string
            {
                try {
                    $validator = new TableValidator($this->table_name);
                    $validator->check();
                } catch (Exception $e) {
                    return 'Произошла ошибка подключения: ' . $e->getMessage();
                }

                $stmt = $this->pdo->prepare("INSERT INTO {$this->table_name} (name) VALUES (:name)");
                $stmt->bindValue(':name', $data["name"]);
                $stmt->execute();

                $id = $this->pdo->lastInsertId();
                $folder = ["id" => $id, "name" => $data["name"]];

                return json_encode($folder);
            }

            /**
             * @throws Exception
             */
            public function renameFolder($id, $newName): bool|string
            {
                try {
                    $validator = new TableValidator( $this->table_name );
                    $validator->check ();
                } catch (Exception $e) {
                    return 'Произошла ошибка подключения: ' . $e->getMessage ();
                }

                $stmt = $this->pdo->prepare ( "UPDATE {$this->table_name}
                        SET name = :newName WHERE id = :id" );
                $stmt->bindValue ( ':newName', $newName );
                $stmt->bindValue ( ':id', $id );
                $stmt->execute ();

                return json_encode ( ["id" => $id, "name" => $newName] );
            }

            /**
             * @throws Exception
             */
            public function getFolder($id): bool|string
            {
                try {
                    $validator = new TableValidator($this->table_name);
                    $validator->check();
                } catch (Exception $e) {
                    return 'Произошла ошибка подключения: ' . $e->getMessage();
                }

                $stmt = $this->pdo->prepare("SELECT id, name FROM 
                    {$this->table_name} WHERE id = :id");
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $folder = $stmt->fetch();

                $stmt = $this->pdo->prepare("SELECT id, name FROM {$this->table_name}
                    WHERE folder_id = :id");
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $files = $stmt->fetchAll();

                $stmt = $this->pdo->prepare("SELECT id, name FROM {$this->table_name}
                    WHERE parent_id = :id");
                $stmt->bindValue(':id', $id);

                $stmt->execute();
                $folders = $stmt->fetchAll();
                $folder["files"] = $files;
                $folder["folders"] = $folders;

                return json_encode($folder);
            }

            /**
             * @throws Exception
             */
            public function removeFolder($id): bool|string
            {
                try {
                    $validator = new TableValidator($this->table_name);
                    $validator->check();
                } catch (Exception $e) {
                    return 'Произошла ошибка подключения: ' . $e->getMessage();
                }

                $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name}
                    WHERE id = :id");
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name}
                    WHERE folder_id = :id");
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name}
                    WHERE parent_id = :id");
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                return json_encode(["message" => "Folder deleted successfully"]);
            }

        }