<?php

namespace Controllers;

    use Models\FileModel;
    use Exception;

    class FilesController
    {
        private FileModel $fileModel;

        public function __construct(FileModel $fileModel)
        {
            $this->fileModel = $fileModel;
        }

        public function fileList()
        {
            try{
                $result = $this->fileModel->fileList();

                if(isset(json_decode($result, true)['error'])) {
                    throw new Exception(json_decode($result, true)['error']);
                }

                return $result;
            } catch (Exception $e) {
                return 'Произошла ошибка: ' . $e->getMessage();
            }
        }

        public function getFile($id)
        {
            try {
                $result = $this->fileModel->getFile($id);

                if(isset(json_decode($result, true)['error'])) {
                    throw new Exception(json_decode($result, true)['error']);
                }

                return $result;
            } catch (Exception $e) {
                return 'Произошла ошибка: ' . $e->getMessage();
            }
        }

        public function addFile($data)
        {
            try {
                $result = $this->fileModel->addFile($data);

                if(isset(json_decode($result, true)['error'])) {
                    throw new Exception(json_decode($result, true)['error']);
                }

                return $result;
            } catch (Exception $e) {
                return 'Произошла ошибка: ' . $e->getMessage();
            }
        }

        public function renameFile($id, $newName)
        {
            try {
                $result = $this->fileModel->renameFile($id, $newName);

                if(isset(json_decode($result, true)['error'])) {
                    throw new Exception(json_decode($result, true)['error']);
                }

                return $result;
            } catch (Exception $e) {
                return 'Произошла ошибка: ' . $e->getMessage();
            }
        }
    }
