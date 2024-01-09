<?php

namespace Controllers;

use Models\FileModel;
use Core\Response;
use Exception;

    class FilesController extends BaseController
    {
        private FileModel $model;

        public function __construct()
        {
            $this->model = new FileModel('files');
            parent::__construct();
        }
        public function listFiles()
        {
            try {
                $files = $this->model->fileList();
                Response::sendJsonResponse($files);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 500);
            }
        }

        public function getFile($id)
        {
            try {
                $file = $this->model->getFile($id);
                Response::sendJsonResponse($file);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 404);
            }
        }

        public function addFile($data)
        {
            try {
                $file = $this->model->addFile($data);
                Response::sendJsonResponse($file, 201);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 400);
            }
        }

        public function renameFile($id, $newName)
        {
            try {
                $result = $this->model->renameFile($id, $newName);
                Response::sendJsonResponse($result);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 400);
            }
        }

        public function removeFile($id)
        {
            try {
                $result = $this->model->removeFile($id);
                Response::sendJsonResponse($result);
            } catch (Exception $e) {
                Response::sendJsonResponse(["error" => $e->getMessage()], 400);
            }
        }
    }
