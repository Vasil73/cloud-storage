<?php

namespace Models;

use Exception;
use PDO;

    class FoldersFileModel extends BaseModel
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
         * Проверяет существование папки по её имени.
         *
         * @param string $folderName Имя папки для проверки.
         * @return bool Возвращает true, если папка существует, иначе false.
         * @throws Exception Если выполнение запроса не удалось.
         */
        public function folderExists(string $folderName): bool
        {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $this->table_name WHERE name = :name");
            $stmt->bindValue(':name', $folderName);
            if(!$stmt->execute()) {
                throw new Exception("Ошибка проверки существования папки.");
            }

            $count = $stmt->fetchColumn();

            return $count > 0;
        }

        /**
         * Добавляет новую папку с предоставленными данными.
         *
         * @param array $data Ассоциативный массив параметров новой папки ('name' и 'parent_id').
         * @return array Ассоциативный массив с данными добавленной папки.
         * @throws Exception Если имя папки не предоставлено или запрос выполнить не удалось.
         */
        public function addFolder(array $data): array
        {
            if (!isset($data['name'])) {
                throw new Exception("Необходимо ввести имя в поле.");
            }

            $stmt = $this->pdo->prepare("INSERT INTO $this->table_name (name, parent_id) VALUES (:name, :parent_id)");
            $stmt->bindValue(':name', $data["name"]);
            $stmt->bindValue(':parent_id', $data["parent_id"]);
            $stmt->execute();

            $id = $this->pdo->lastInsertId();
            return ["id" => $id, "name" => $data["name"], "parent_id" => $data['parent_id']];
        }

        /**
         * Переименовывает папку с указанным ID.
         *
         * @param int $id ID папки для переименования.
         * @param string $newName Новое имя папки.
         * @return array Ассоциативный массив с обновлённой информацией папки.
         * @throws Exception Если запрос выполнить не удалось.
         */
        public function renameFolder(int $id, string $newName): array
        {
            $stmt = $this->pdo->prepare("UPDATE $this->table_name SET name = :name WHERE id = :id");
            $stmt->bindValue(':name', $newName);
            $stmt->bindValue(':id', $id);
            if(!$stmt->execute()) {
                throw new Exception("Ошибка обновления имени папки..");
            }

            return ["id" => $id, "name" => $newName]; // "id" => $id,
        }

        /**
         * Получает информацию о папке и её содержимом по ID.
         *
         * @param int $id Уникальный идентификатор папки.
         * @return array|null Массив с информацией о папке и её содержимом или null, если папка не найдена.
         */
        public function getFolder(int $id)
        {
            $folderStmt = $this->pdo->prepare("SELECT id, name, parent_id FROM folders WHERE id = :id");
            $folderStmt->bindValue(':id', $id);
            $folderStmt->execute();
            $folder['folders'] = $folderStmt->fetchAll(PDO::FETCH_ASSOC);



            if ($folder) {
                $filesStmt = $this->pdo->prepare("SELECT id, name FROM files WHERE folder_id = :id");
                $filesStmt->bindValue(':id', $id);
                $filesStmt->execute();
                $subFiles = $filesStmt->fetchAll(PDO::FETCH_ASSOC);
                $folder['files'] = $subFiles;
           }

            return $folder ?: null;
        }

        /**
         * Удаляет папку по её ID.
         *
         * @param int $id ID удаляемой папки.
         * @return int ID удалённой папки.
         * @throws Exception Если запрос на удаление не удался.
         */
        public function removeFolder(int $id)
        {
            $stmt = $this->pdo->prepare("DELETE FROM $this->table_name WHERE id = :id");
            $stmt->bindValue(':id', $id);

            if(!$stmt->execute()) {
                throw new Exception("Ошибка удаления папки.");
            }

            $stmt = $this->pdo->prepare("DELETE FROM $this->table_name WHERE id = :id");
            $stmt->bindValue(':id', $id);

            if(!$stmt->execute()) {
                throw new Exception("Ошибка удаления содержимого папки.");
            }
            return $id;

        }

        /**
         * Перемещает файл в другую папку.
         *
         * @param int $fileId ID файла, который нужно переместить.
         * @param int $targetFolderId ID папки, в которую нужно переместить файл.
         * @return bool Возвращает true, если операция успешна.
         * @throws Exception Exception Если целевая папка не существует или запрос не может быть выполнен.
         */
        public function moveFile(int $fileId, int $targetFolderId): bool {
            $folderStmt = $this->pdo->prepare("SELECT COUNT(*) FROM $this->table_name WHERE id = :id");
            $folderStmt->bindValue(':id', $targetFolderId);
            $folderStmt->execute();

            if ($folderStmt->fetchColumn() <= 0) {
                throw new Exception("Целевая папка не существует.");
            }

            $fileStmt = $this->pdo->prepare("UPDATE $this->table_name SET folder_id = :folder_id WHERE id = :file_id");
            $fileStmt->bindValue(':folder_id', $targetFolderId);
            $fileStmt->bindValue(':file_id', $fileId);

            return $fileStmt->execute();
        }

        /**
         * Перемещает папку в другую папку.
         *
         * @param int $folderId ID папки, которую нужно переместить.
         * @param int $targetFolderId ID папки, в которую нужно переместить папку. Если 0, папка будет перемещена в корневой каталог.
         * @return bool Возвращает true, если операция успешна.
         * @throws Exception Если целевая папка не существует или запрос не может быть выполнен.
         */
        public function moveFolder(int $folderId, int $targetFolderId): bool {
            // TODO: реализовать проверку на циклические ссылки, если папка пытается переместиться в одну из своих подпапок.

            // Если $targetFolderId не равен 0, проверяем, существует ли целевая папка.
            if ($targetFolderId !== 0) {
                $targetFolderStmt = $this->pdo->prepare("SELECT COUNT(*) FROM $this->table_name WHERE id = :id");
                $targetFolderStmt->bindValue(':id', $targetFolderId);
                $targetFolderStmt->execute();

                if ($targetFolderStmt->fetchColumn() <= 0) {
                    throw new Exception("Целевая папка не существует.");
                }
            }

            // Обновляем parent_id для перемещаемой папки.
            $folderStmt = $this->pdo->prepare("UPDATE $this->table_name SET parent_id = :parent_id WHERE id = :folder_id");
            $folderStmt->bindValue(':parent_id', $targetFolderId === 0 ? NULL : $targetFolderId);
            $folderStmt->bindValue(':folder_id', $folderId);

            return $folderStmt->execute();
        }

    }
