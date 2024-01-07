<?php

namespace Models;

    use Core\Database;
    use Core\TableValidator;
    use Exception;
    use PDO;
    use PDOException;

        class FileModel
        {
            public PDO $pdo;

            private string $table_name;

            public function __construct(string $table_name)
            {
                $this->table_name = $table_name;
                $this->pdo = Database::getInstance();
            }

            /**
             * @throws Exception
             */
            public function fileList()
            {
                $validator = new TableValidator($this->table_name);
                $validator->check();

                $stmt = $this->pdo->prepare("SELECT id, name FROM `{$this->table_name}`");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            /**
             * @throws Exception
             */
            public function getFile($id)
            {
                $validator = new TableValidator($this->table_name);
                $validator->check();

                $stmt = $this->pdo->prepare("SELECT id, name, size FROM `{$this->table_name}` WHERE id = :id");
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $file = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($file === false) {
                    throw new Exception("Файл с указанным идентификатором не найден.");
                }

                return $file;
            }

            /**
             * @throws Exception
             */
            public function addFile($data)
            {
                $name = trim($data["name"]);
                $size = intval($data["size"]);

                if ($name === '' || $size <= 0) {
                    throw new Exception("Предоставлены неверные данные.");
                }

                $validator = new TableValidator($this->table_name);
                $validator->check();

                $stmt = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, size) VALUES (:name, :size)");
                $stmt->bindValue(':name', $name);
                $stmt->bindValue(':size', $size);

                if(!$stmt->execute()) {
                    throw new Exception("Ошибка выполнения запроса.");
                }

                // Метод теперь возвращает массив с информацией о файле.
                return ["id" => $this->pdo->lastInsertId(), "name" => $name, "size" => $size];
            }

            /**
             * @throws Exception
             */
            public function renameFile($id, $newName)
            {
                $validator = new TableValidator($this->table_name);
                $validator->check();

                if (!is_scalar($newName)) {
                    throw new Exception("Новое имя должно быть скалярным значением..");
                }

                $stmt = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = :newName WHERE id = :id");
                $stmt->bindValue(':newName', $newName);
                $stmt->bindValue(':id', $id);

                if(!$stmt->execute()) {
                    throw new Exception("Ошибка обновления имени файла..");
                }

                return ["id" => $id, "name" => $newName];
            }

            /**
             * @throws Exception
             */
            public function removeFile($id)
            {
                $validator = new TableValidator($this->table_name);
                $validator->check();

                $stmt = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = :id");
                $stmt->bindValue(':id', $id);

                if(!$stmt->execute()) {
                    throw new Exception("Ошибка удаления файла.");
                }

                return ["message" => "Файл успешно удален"];
            }
        }
