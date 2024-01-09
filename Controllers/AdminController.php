<?php

namespace Controllers;

use Core\Response;
use Exception;
use Models\AdminModel;
use PDOException;

    class AdminController extends BaseController
    {
        private AdminModel $model;

        public function __construct()
        {
            $this->model = new AdminModel('users');
            parent::__construct();
        }

        /**
         * Получить список пользователей.
         *
         * @param int $id Идентификатор для фильтрации списка пользователей.
         *
         * @return void
         * @throws Exception Если запрос не может быть выполнен.
         */
        public function getUserList(int $id)
        {

            try {
              $users = $this->model->getUsers($id);
                Response::sendJsonResponse(['users' => $users], 200);
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => $e->getMessage()], $e->getCode());
            }
        }

        /**
         * Удалить пользователя по идентификатору.
         *
         * @param int $id ID пользователя для удаления.
         *
         * @return void
         * @throws Exception Если удаление не удалось.
         */
        public function deleteUser(int $id)
        {
            try {
                $result = $this->model->deleteUser($id);
                if ($result) {
                    Response::sendJsonResponse(['message' => 'Пользователь успешно удален.'], 200);
                } else {
                    Response::sendJsonResponse(['error' => 'Не удалось удалить пользователя.'], 400);
                }
            } catch (Exception $e) {
                $this->handleException ($e);
            }

        }

        /**
         * Обновить информацию о пользователе администратором.
         *
         * Принимает массив данных, который должен содержать 'id', 'name', 'email', 'age' и 'gender'.
         * Возвращает JSON-ответ со статусом операции.
         *
         * @param array $data Массив данных для обновления пользователя.
         *
         * @return void
         * @throws PDOException Если произошла ошибка БД.
         */
        public function updateUserAdmin(array $data): void
        {
            if (!isset( $data[ 'id' ] )) {
                Response::sendJsonResponse ( ['error' => 'Параметр id обязателен'], 400 );
            }
            $cleanData = $this->jsonRequest->getData ();

            if (!isset( $cleanData[ 'name' ], $cleanData[ 'email' ], $cleanData[ 'age' ], $cleanData[ 'gender' ] )) {
                Response::sendJsonResponse ( ['error' => 'Необходимы параметры: name, email, age, gender'], 400 );
            }

            try {
                $isUpdated = $this->model->updateUser ( $data[ 'id' ], $cleanData );
                if ($isUpdated) {
                    Response::sendJsonResponse ( ['status' => "Пользователь успешно обновлен"], 200 );
                } else {
                    Response::sendJsonResponse ( ['status' => "Ошибка при обновлении пользователя"], 400 );
                }
            } catch (PDOException $ex) {
                 Response::sendJsonResponse ( ["error" => "Внутренняя ошибка сервера"], 500 );
                return;
            }
        }
    }
