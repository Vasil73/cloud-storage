<?php

namespace Controllers;

    use Core\Response;
    use Models\AdminModels;

    class AdminController
    {
        private AdminModels $adminModel;

        public function __construct()
        {
            $this->adminModel = new AdminModels();
        }
        public function getUserList($adminId): array
        {

            return $this->adminModel->getUserList ($adminId);
        }

        public function updateUser($id, $data): bool
        {
            return $this->adminModel->updateUser ($id, $data);
        }

        public function deleteUsers($adminId)
        {
            return $this->adminModel->deleteUsers ($adminId);
        }
    }