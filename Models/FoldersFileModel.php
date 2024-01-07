<?php

namespace Models;

    use Core\Database;
    use Core\TableValidator;
    use Exception;
    use PDO;

    class FoldersFileModel
    {
        public PDO $pdo;
        private string $table_name;

        /**
         * @param string $table_name
         */
        public function __construct(string $table_name)
        {
           $this->table_name = $table_name;
            $this->pdo = Database::getInstance ();
        }

        /**
         * @param string $folderName
         * @return bool
         * @throws Exception
         */

        public function folderExists(string $folderName): bool
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table_name} WHERE name = :name");
            $stmt->bindValue(':name', $folderName);
            if(!$stmt->execute()) {
                throw new Exception("Ошибка проверки существования папки.");
            }

            $count = $stmt->fetchColumn();

            return $count > 0;
        }

        /**
         * @param array $data
         * @return array
         * @throws Exception
         */
        public function addFolder(array $data): array
        {

            $validator = new TableValidator($this->table_name);
            $validator->check();

            if (!isset($data['name'])) {
                throw new Exception("Необходимо ввести имя в поле.");
            }

            $stmt = $this->pdo->prepare("INSERT INTO {$this->table_name} (name, parent_id) VALUES (:name, :parent_id)");
            $stmt->bindValue(':name', $data["name"]);
            $stmt->bindValue(':parent_id', $data["parent_id"]);
            $stmt->execute();

            $id = $this->pdo->lastInsertId();
            return ["id" => $id, "name" => $data["name"], "parent_id" => $data['parent_id']];
        }

        /**
         * @param int $id
         * @param string $newName
         * @return array
         * @throws Exception
         */
        public function renameFolder(int $id, string $newName): array
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $stmt = $this->pdo->prepare("UPDATE {$this->table_name} SET name = :newName WHERE id = :id");
            $stmt->bindValue(':newName', $newName);
            $stmt->bindValue(':id', $id);
            if(!$stmt->execute()) {
                throw new Exception("Ошибка обновления имени папки..");
            }

            return ["id" => $id, "name" => $newName];
        }

        /**
         * @param int $id
         * @return array
         * @throws Exception
         */
        public function getFolder(int $id): array
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $stmt = $this->pdo->prepare("SELECT id, name FROM {$this->table_name} WHERE id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $folder = $stmt->fetch(PDO::FETCH_ASSOC);

            // Если папка не найдена, возвращается пустой массив
            if (!$folder) {
                return [];
            }

            $stmt = $this->pdo->prepare("SELECT id, name FROM {$this->table_name} WHERE folder_id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->prepare("SELECT id, name FROM {$this->table_name} WHERE parent_id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $folder["files"] = $files;
            $folder["folders"] = $folders;

            return $folder;
        }

        /**
         * @param int $id
         * @throws Exception
         */
        public function removeFolder(int $id)
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = :id");
            $stmt->bindValue(':id', $id);

            if(!$stmt->execute()) {
                throw new Exception("Ошибка удаления папки.");
            }

            $stmt = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE folder_id = :id OR parent_id = :id");
            $stmt->bindValue(':id', $id);

            if(!$stmt->execute()) {
                throw new Exception("Ошибка удаления содержимого папки.");
            }
            return $id;

        }

    }
