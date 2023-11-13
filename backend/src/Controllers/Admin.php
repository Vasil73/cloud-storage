<?php

namespace App\Controllers;

use Exception;
use PDO;

    class Admin extends User
    {
        private function isUserAdmin($userId): bool
        {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = 'admin' AND id = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return true;
            }
            return false;
        }

        public function getUserList($adminId): bool|string
        {
            try {

                if (!$this->isUserAdmin($adminId)) {
                    return json_encode(['error' => 'Unauthorized Access']);
                }

            } catch (Exception $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
            return parent::getUsers ();
        }

        /**
         * @throws Exception
         */
        public function deleteUsers($adminId): void
        {
            if (!$this->isUserAdmin($adminId)) {
                throw new Exception('Unauthorized Access', 403);
            }

            $query = "DELETE FROM users WHERE id = :userId";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute([':userId' => $userId]);
        }

        public function updateUser($id, $data): bool
        {
            if (!$this->isUserAdmin($id)) {
                return json_encode(['error' => 'Unauthorized Access']);
            }

            return parent::updateUser($id, $data);
        }
    }