<?php

namespace Controllers;

    use Exception;
    use Models\UserModel;
    use PDO;

    class UserController
    {
        private UserModel $userModel;

        public function __construct(UserModel $userModel)
        {
            $this->userModel = $userModel;
        }

//        public function __construct(PDO $pdo)
//        {
//            $this->userModel = new UserModel($pdo);
//        }

        public function getUsers(): string
        {
            return $this->userModel->getUsers();
        }

        public function getUserById($userId)
        {
            return $this->userModel->getUserById($userId);
        }

        /**
         * @throws Exception
         */
        public function searchByEmail($email)
        {
            return $this->userModel->searchByEmail($email);
        }

        public function updateUser($id, $data)
        {
            return $this->userModel->updateUser($id, $data);
        }

        public function deleteUser($id)
        {
            $this->userModel->deleteUser($id);
        }
    }
