<?php

namespace Controllers;

    use Core\TableValidator;
    use Exception;
    use Models\FileAccessModel;

    class FileAccessController
    {
        private string $table_name;
        private FileAccessModel $model;

        public function __construct( $table_name)
        {
            $this->table_name = $table_name;
            $this->model = new FileAccessModel($table_name);
        }

        public function addSharedUsers($file_id, $user_id): string
        {
            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();

                if ($this->model->addSharedUser($file_id, $user_id)) {
                    return json_encode ( ["message" => "Пользователь успешно добавлен в общий доступ"] );
                }
                return "Не удалось добавить пользователя для общего доступа.";
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }
        }

        public function getSharedUsers($fileId): string
        {
            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();

                $users = $this->model->getSharedUsers($fileId);
                if (!empty( $users )) {
                    return json_encode ( $users );
                }
                return 'Для этого файла не найдено общих пользователей';
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }
        }

        public function removeSharedUser($file_id, $user_id)
        {
            try {
                $validator = new TableValidator( $this->table_name );
                $validator->check ();

                return $this->model->removeSharedUser($file_id, $user_id);
            } catch (Exception $e) {
                return 'Произошла ошибка подключения: ' . $e->getMessage ();
            }
        }
    }