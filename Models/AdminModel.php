<?php

namespace Models;

    use Core\TableValidator;
    use Exception;
    use PDO;

    class AdminModel extends UserModel
    {

       public string $table_name;

        /**
         * @throws Exception
         */
        private function isUserAdmin($userId): bool
        {
            $validator = new TableValidator($this->table_name);
            $validator->check();

            $stmt = $this->pdo->prepare("SELECT * FROM `{ $this->table_name }` WHERE role = 'admin' AND id = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result && $result['admin'] == 'admin';
        }

        /**
         * @throws Exception
         */
        public function getUserList($adminId): array
        {
            if (!$this->isUserAdmin($adminId)) {
                throw new \Exception ( 'Unauthorized Access', 403 );
            }
            return parent::getUsers ();
        }

        /**
         * @throws Exception
         */
        public function deleteUser($adminId, $userId)
        {
            if (!$this->isUserAdmin($adminId)) {
                throw new Exception('Unauthorized Access', 403);
            }
            return parent::deleteUserById ($userId);
        }

        /**
         * @throws Exception
         */
        public function updateUser($id, $data): bool
        {
            if (!$this->isUserAdmin($id)) {
                throw new Exception('Unauthorized Access', 403);
            }
            $cleanData = $this->filterData($data);

            return parent::updateUser($id, $cleanData);
        }

        private function filterData(array $data): array
        {
            foreach ($data as $key => &$value) {
                if (is_string($value)) {
                    $value[$key] = strtolower(trim(htmlspecialchars($value, ENT_QUOTES)));
                }
            }
            return $data;
        }

    }
