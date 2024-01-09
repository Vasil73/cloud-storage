<?php

namespace Controllers;

use Exception;
use Models\FileAccessModel;
use Core\Response;
use PDOException;

    class FileAccessController extends BaseController
    {
        private FileAccessModel $model;

        public function __construct()
        {
            $this->model = new FileAccessModel('file_user');
            parent::__construct();
        }

        public function addSharedUsers($file_id, $user_id)
        {
            try {
                if ($this->model->addSharedUser($file_id, $user_id)) {
                    Response::sendJsonResponse(["message" => "Пользователь успешно добавлен в общий доступ"], 200);
                } else {
                    Response::sendJsonResponse(["error" => "Не удалось добавить пользователя для общего доступа."], 400);
                }
            } catch (PDOException $ex) {
                Response::sendJsonResponse(["error" => "Внутренняя ошибка сервера: " . $ex->getMessage()], 500);
            } catch (Exception $e) {
            }
        }

        public function getSharedUsers($fileId)
        {
            try {
                $users = $this->model->getSharedUsers($fileId);
                if (!empty($users)) {
                    Response::sendJsonResponse($users, 200);
                } else {
                    Response::sendJsonResponse(["message" => "Для этого файла не найдено общих пользователей."], 404);
                }
            } catch (PDOException $e) {
                Response::sendJsonResponse(["error" => "Произошла ошибка подключения: " . $e->getMessage()], 500);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 500);
            }
        }

        public function removeSharedUser($file_id, $user_id)
        {
            try {
                if ($this->model->removeSharedUser($file_id, $user_id)) {
                    Response::sendJsonResponse(["message" => "Пользователь успешно удален из общего доступа."], 200);
                } else {
                    Response::sendJsonResponse(["error" => "Не удалось удалить пользователя из общего доступа."], 400);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => "Произошла ошибка: " . $e->getMessage()], 500);
            }
        }
    }
