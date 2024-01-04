<?php

namespace Models;

    use Core\Response;
    use Exception;
    use PDO;

    class AdminModels extends UserModel
    {

        private function isUserAdmin($userId): bool
        {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = 'admin' AND id = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result && $result['userId'] == 1;
        }
        public function getUserList($adminId): array
        {
            try {
                if (!$this->isUserAdmin($adminId)) {
                    Response::sendJsonResponse (['error' => 'Unauthorized Access']);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse (["error" => "Внутренняя ошибка сервера"], 500);
            }
            return parent::getUsers ();
        }

        /**
         * @throws Exception
         */
        public function deleteUsers($adminId, $userId)
        {
            if (!$this->isUserAdmin($adminId)) {
                throw new Exception('Unauthorized Access', 403);
            }

            return parent::deleteUserById ($userId);
        }

        public function updateUser($id, $data): bool
        {
            if (!$this->isUserAdmin($id)) {
                Response::sendJsonResponse (['error' => 'Unauthorized Access']);
            }
            $cleanData = $this->filterData($data);

            return parent::updateUser($id, $cleanData);
        }

        private function filterData(array $data): array
        {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = htmlspecialchars ($value, ENT_QUOTES);
                }
                if (is_string($value)) {
                    $data[$key] = trim($value);
                }
                if (is_string($value)) {
                    $data[$key] = strtolower($value);
                }
            }

            return $data;
        }
    }