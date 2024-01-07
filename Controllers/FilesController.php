<?php

namespace Controllers;

    use Models\FileModel;
    use Core\Response;
    use Exception;

    class FilesController
    {
        private FileModel $fileModel;

        public function __construct()
        {
            $this->fileModel = new FileModel('file');
        }

        public function listFiles()
        {
            try {
                $files = $this->fileModel->fileList();
                Response::sendJsonResponse($files);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 500);
            }
        }

        public function getFile($id)
        {
            try {
                $file = $this->fileModel->getFile($id);
                Response::sendJsonResponse($file);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 404);
            }
        }

        public function addFile($data)
        {
            try {
                $file = $this->fileModel->addFile($data);
                Response::sendJsonResponse($file, 201);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 400);
            }
        }

        public function renameFile($id, $newName)
        {
            try {
                $result = $this->fileModel->renameFile($id, $newName);
                Response::sendJsonResponse($result);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 400);
            }
        }

        public function removeFile($id)
        {
            try {
                $result = $this->fileModel->removeFile($id);
                Response::sendJsonResponse($result);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 400);
            }
        }
    }
