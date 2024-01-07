<?php

namespace Controllers;

    use Core\App;
    use Models\AdminModel;
    use Core\Response;
    use Exception;

    class AdminController
    {
        private AdminModel $adminModel;

        public function __construct(string $table_name)
        {
            $this->adminModel = new AdminModel($table_name);
        }

        public function getUserList($adminId)
        {
            try {
                $users = $this->adminModel->getUserList($adminId);
                Response::sendJsonResponse(['users' => $users], 200);
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => $e->getMessage()], $e->getCode());
            }
        }

        public function deleteUser($adminId, $userId)
        {
            try {
                $result = $this->adminModel->deleteUser($adminId, $userId);
                if ($result) {
                    Response::sendJsonResponse(['message' => 'Пользователь успешно удален.'], 200);
                } else {
                    Response::sendJsonResponse(['error' => 'Не удалось удалить пользователя.'], 400);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => $e->getMessage()], $e->getCode());
            }
        }

        public function updateUser($adminId, $userId, $data)
        {
            try {
                $result = $this->adminModel->updateUser($userId, $data);
                if ($result) {
                    Response::sendJsonResponse(['message' => 'Пользователь обновлен успешно.'], 200);
                } else {
                    Response::sendJsonResponse(['error' => 'Не удалось обновить пользователя.'], 400);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => $e->getMessage()], $e->getCode());
            }
        }
    }
