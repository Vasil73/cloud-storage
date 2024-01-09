<?php

namespace Controllers;

use Core\Response;
use Exception;
use Models\FoldersFileModel;

    class FoldersFileController extends BaseController
    {
        private FoldersFileModel $model;

        public function __construct()
        {
            $this->model = new FoldersFileModel('folders');
            parent::__construct();
        }

        public function addFolder()
        {
            try {
                $data = $this->jsonRequest->getData();
                $folderName = $data['name'];

                if ($this->model->folderExists($folderName)) {
                    Response::sendJsonResponse(['error' => 'Папка с таким именем уже существует.'], 400);
                    return;
                }
                $response = $this->model->addFolder($data);
                Response::sendJsonResponse($response);

            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }
        }

        public function renameFolder()
        {
            try {
                $id = $this->jsonRequest->get('id');
                $newName = $this->jsonRequest->get('name');
                if (is_null($id) ||is_null($newName)) { //is_null($id) ||
                    throw new Exception('Необходимы параметры id и name.');
                }
                $response = $this->model->renameFolder((int)$id, $newName);
                Response::sendJsonResponse($response);
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }
        }

        public function getFolderId($id)
        {
            try {
                $response = $this->model->getFolder((int)$id);
                if ($response !== null) {
                    Response::sendJsonResponse ($response);
                } else {
                   Response::sendJsonResponse (["massage" => "Папка не найдена"], 404);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }

        }

        public function removeFolder()
        {
            try {
                $id = $this->jsonRequest->get('id');
                if (is_null($id)) {
                    throw new Exception('Необходим параметр id.');
                }
                $response = $this->model->removeFolder((int)$id);
                if ($response) {
                    Response::sendJsonResponse (['message' => "Папка успешно удалена!"]);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }
        }
        public function moveFile()
        {
            try {
                $data = $this->jsonRequest->getData();
                $fileId = $data['fileId'] ?? null; // `??` - оператор объединения с null
                $targetFolderId = $data['targetFolderId'] ?? null;

                if ($fileId === null || $targetFolderId === null) {
                    throw new Exception('Необходимы параметры fileId и targetFolderId.');
                }

                $response = $this->model->moveFile((int)$fileId, (int)$targetFolderId);
                if ($response) {
                    Response::sendJsonResponse(['message' => 'Файл успешно перемещен.']);
                } else {
                    Response::sendJsonResponse(['error' => 'Перемещение файла не удалось.'], 500);
                }

            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }
        }

        public function moveFolder()
        {
            try {
                $data = $this->jsonRequest->getData();
                $folderId = $data['folderId'] ?? null;
                $targetFolderId = $data['targetFolderId'] ?? null;

                if ($folderId === null || $targetFolderId === null) {
                    throw new Exception('Необходимы параметры folderId и targetFolderId.');
                }

                $response = $this->model->moveFolder((int)$folderId, (int)$targetFolderId);
                if ($response) {
                    Response::sendJsonResponse(['message' => 'Папка успешно перемещена.']);
                } else {
                    Response::sendJsonResponse(['error' => 'Перемещение папки не удалось.'], 500);
                }

            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }
        }

        private function isValidId($id): bool
        {
            return is_numeric($id) && (int)$id > 0;
        }

    }
