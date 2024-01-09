<?php

namespace Controllers;

use Core\JsonRequest;
use Core\Response;
use InvalidArgumentException;
use Models\UserModel;
use Exception;
use PDOException;

    class UserController extends BaseController
    {
        private UserModel $model;

        public function __construct()
        {
            $this->model = new UserModel('users');
            parent::__construct();
        }

        public function updateUser($params ): void
        {
            if (!isset( $params[ 'id' ] )) {
                Response::sendJsonResponse ( ['error' => 'Параметр id обязателен'], 400 );
            }
            $jsonRequest = new JsonRequest();
            $input = $jsonRequest->getData ();

            if (!isset( $input[ 'name' ], $input[ 'email' ], $input[ 'age' ], $input[ 'gender' ] )) {
                Response::sendJsonResponse ( ['error' => 'Необходимы параметры: name, email, age, gender'], 400 );
            }

            try {
                $isUpdated = $this->model->updateUser ( $params[ 'id' ], $input );
                if ($isUpdated) {
                    Response::sendJsonResponse ( ['status' => "Пользователь успешно обновлен"] );
                } else {
                    Response::sendJsonResponse ( ['status' => "Ошибка при обновлении пользователя"], 400 );
                }

            } catch (PDOException $ex) {
                Response::sendJsonResponse ( ["error" => "Внутренняя ошибка сервера"], 500 );
                return;
            } catch (Exception $e) {
            }
        }

        public function getUsers()
        {
            try {
                $users = $this->userModel->getUsers();
                if ($users) {
                    Response::sendJsonResponse ( $users );
                }
            } catch (PDOException $ex) {
                Response::sendJsonResponse(["error" => "Внутренняя ошибка сервера"], 500);
            }
        }

        public function getUserId($id)
        {
            try {
                $user = $this->userModel->getUserById($id);
                if ($user) {
                    Response::sendJsonResponse ( $user );
                } else {
                    Response::sendJsonResponse (["massage" => "Пользователь не найден"], 404);
                }
            }
            catch (PDOException $ex) {
                Response::sendJsonResponse(["error" => "Внутренняя ошибка сервера"], 500);
            }
        }

        public function searchByEmail($email)
        {
            try {
                $user = $this->userModel->searchByEmail($email);
                if ($user) {
                    Response::sendJsonResponse($user);
                } else {
                    Response::sendJsonResponse(["message" => "Пользователь не найден"], 404);
                }
            } catch (InvalidArgumentException $ex) {
                Response::sendJsonResponse(["error" => $ex->getMessage()], 400);
            } catch (PDOException $ex) {
                Response::sendJsonResponse(["error" => "Внутренняя ошибка сервера"], 500);
            } catch (Exception $ex) {
                Response::sendJsonResponse(["error" => $ex->getMessage()], 500);
            }
        }

        public function deleteUser($id): void
        {
            try {
                $userDelete = $this->model->deleteUserById($id);
                if ($userDelete) {
                    Response::sendJsonResponse ( true );
                } else {
                    Response::sendJsonResponse (["massage" => "Пользователь не найден"], 404);
                }
            }
            catch (PDOException $ex) {
                Response::sendJsonResponse(["error" => "Внутренняя ошибка сервера"], 500);
            } catch (\JsonException $e) {
            }
        }

    }