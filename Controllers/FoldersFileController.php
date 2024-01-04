<?php

namespace Controllers;

    use Exception;
    use Models\FoldersFileModel;

    class FoldersFileController
    {
        private FoldersFileModel $model;

        public function __construct($table_name)
        {
            $this->model = new FoldersFileModel($table_name);
        }

        public function addFolder($data)
        {
            try {
                $response = $this->model->addFolder($data);
                echo $response;
            } catch (Exception $e) {
                echo 'Произошла ошибка: ' . $e->getMessage();
            }
        }

        public function renameFolder($id, $newName)
        {
            try {
                $response = $this->model->renameFolder($id, $newName);
                echo $response;
            } catch (Exception $e) {
                echo 'Произошла ошибка: ' . $e->getMessage();
            }
        }

        public function getFolder($id)
        {
            try {
                $response = $this->model->getFolder($id);
                echo $response;
            } catch (Exception $e) {
                echo 'Произошла ошибка: ' . $e->getMessage();
            }
        }

        public function removeFolder($id)
        {
            try {
                $response = $this->model->removeFolder($id);
                echo $response;
            } catch (Exception $e) {
                echo 'Произошла ошибка: ' . $e->getMessage();
            }
        }
    }