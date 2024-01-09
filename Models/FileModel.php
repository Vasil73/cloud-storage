<?php

namespace Models;

use Exception;
use PDO;

    class FileModel extends BaseModel
    {
        /**
         * @param string $table_name
         * @throws Exception
         */
        public function __construct(string $table_name)
        {
            parent::__construct($table_name);
        }

        private function encryptFileName($filename)
        {
            return base64_encode($filename);
        }

        private function decryptFileName($encryptedFilename)
        {
            return base64_decode($encryptedFilename);
        }

        /**
         * @throws Exception
         */
        public function fileList()
        {
            $stmt = $this->pdo->prepare("SELECT id, name FROM $this->table_name");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * @throws Exception
         */
        public function getFile($id)
        {

            $stmt = $this->pdo->prepare("SELECT id, name, size FROM $this->table_name WHERE id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($file === false) {
                throw new Exception("Файл с указанным идентификатором не найден.");
            }

            $file['name'] = $this->decryptFileName ($file['name']);

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

            $encryptedName = $this->encryptFileName ($name);

            $stmt = $this->pdo->prepare("INSERT INTO $this->table_name (name, size) VALUES (:name, :size)");
            $stmt->bindValue(':name', $encryptedName);
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
            if (!is_scalar($newName)) {
                throw new Exception("Новое имя должно быть скалярным значением..");
            }

            $encryptedNewName = $this->encryptFileName ($newName);

            $stmt = $this->pdo->prepare("UPDATE $this->table_name SET name = :newName WHERE id = :id");
            $stmt->bindValue(':newName', $encryptedNewName);
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

            $stmt = $this->pdo->prepare("DELETE FROM $this->table_name WHERE id = :id");
            $stmt->bindValue(':id', $id);

            if(!$stmt->execute()) {
                throw new Exception("Ошибка удаления файла.");
            }

            return ["message" => "Файл успешно удален"];
        }
    }
