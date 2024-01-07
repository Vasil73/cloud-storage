<?php

namespace Controllers;

    use Core\Handler;
    use Core\JsonRequest;
    use Core\Response;
    use Exception;
    use Models\FoldersFileModel;

    class FoldersFileController
    {
        private FoldersFileModel $model;
        private JsonRequest $jsonRequest;

        public function __construct()
        {
            $this->model = new FoldersFileModel('folders');
            $this->jsonRequest = new JsonRequest();
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
                $newName = $this->jsonRequest->get('newName');
                if (is_null($id) || is_null($newName)) {
                    throw new Exception('Необходимы параметры id и newName.');
                }
                $response = $this->model->renameFolder((int)$id, $newName);
                Response::sendJsonResponse($response);
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }
        }

        public function getFolder()
        {
            try {
                $id = $this->jsonRequest->get('id');
                if (is_null($id)) {
                    throw new Exception('Необходим параметр id.');
                }
                $response = $this->model->getFolder((int)$id);
                Response::sendJsonResponse($response);
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
                Response::sendJsonResponse($response);
                if ($response) {
                    Response::sendJsonResponse (['message' => "Папка успешно удалена!"]);
                }
            } catch (Exception $e) {
                Response::sendJsonResponse(['error' => 'Произошла ошибка: ' . $e->getMessage()], 500);
            }
        }

    }
