<?php

namespace Controllers;

    use Models\AdminModel;
    use Core\Response;
    use Exception;

    class AdminController
    {
        private $adminModel;

        public function __construct()
        {
            $this->adminModel = new AdminModel();
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
                    Response::sendJsonResponse(['message' => 'User deleted successfully.'], 200);
                } else {
                    Response::sendJsonResponse(['error' => 'Failed to delete user.'], 400);
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
                    Response::sendJsonResponse(['message' => 'User updated successfully.'], 200);
                } else {
                    Response::sendJsonResponse(['error' => 'Failed to update user.'], 400);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => $e->getMessage()], $e->getCode());
            }
        }
    }
