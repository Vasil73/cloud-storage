<?php

namespace Models;

use Exception;
use PDO;

    class AdminModel extends BaseModel
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
        private function isUserAdmin($id): bool
        {
            $stmt = $this->pdo->prepare("SELECT * FROM $this->table_name WHERE role = 'admin' AND id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result && $result['role'] == 'admin';
        }

        /**
         * @param $id
         * @return array
         * @throws Exception
         */
        public function getUsers($id): array
        {
            if ($this->isUserAdmin($id)) {
                $stmt = $this->pdo->prepare("SELECT * FROM $this->table_name");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new \Exception ( 'Unauthorized Access', 403 );
            }
        }

        /**
         * @param int $id
         * @return bool
         * @throws Exception
         */
        public function deleteUser(int $id)
        {
            if ($this->isUserAdmin($id)) {
                $stmt = $this->pdo->prepare ( "DELETE FROM $this->table_name WHERE id = :id" );
                $stmt->bindParam ( ':id', $id, PDO::PARAM_INT );
                return $stmt->execute ();
            } else {
                throw new Exception('Unauthorized Access', 403);
            }
        }

        /**
         * *
         * @param int $id
         * @param array $data
         * @return bool
         * @throws Exception
         */
        public function updateUser(int $id, array $data): bool
        {
            if ($this->isUserAdmin($id)) {
                $cleanData = $this->filterData($data);

                $name = $cleanData['name'];
                $email = $cleanData['email'];
                $age = $cleanData['age'];
                $gender = $cleanData['gender'];

                $query = $this->pdo->prepare("UPDATE $this->table_name SET name = :name, email = :email, age = :age, gender = :gender WHERE id = :id");
                $query->bindParam(":id", $id, PDO::PARAM_INT);
                $query->bindParam(":name", $name, PDO::PARAM_STR);
                $query->bindParam(":email", $email, PDO::PARAM_STR);
                $query->bindParam(":age", $age, PDO::PARAM_INT);
                $query->bindParam(":gender", $gender, PDO::PARAM_STR);
                $query->execute();

                // Предполагая, что мы хотим убедиться, что обновление произошло.
                return $query->rowCount() > 0;
            } else {
                throw new Exception('Unauthorized Access', 403);
            }
        }

        private function filterData(array $data): array
        {
            foreach ($data as $key => &$value) {
                if (is_string($value)) {
                    $value = strtolower(trim(htmlspecialchars($value, ENT_QUOTES)));
                }
            }
            return $data;
        }

    }
