<?php

namespace Core;

    use Exception;

    class Request
    {
        private array $data;

        public function __construct()
        {
            $this->data = $this->parseRequestData();
        }

        /**
         * @throws Exception
         */
        private function parseRequestData()
        {
            $method = $_SERVER['REQUEST_METHOD'];
            $body = file_get_contents('php://input');
            $data = [];

            if ($body) {
                $data = json_decode($body, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new Exception('Не удалось разобрать тело запроса в формате JSON');
                }
            }

            // Если метод GET или POST, объединяем данные в $_REQUEST с $data
            if ($method == 'GET' || $method == 'POST') {
                $data = array_merge($_REQUEST, $data);
            }

            return $data;
        }

        public function getData(string $key = null)
        {
            if ($key === null) {
                return $this->data;
            }

            return $this->data[$key] ?? null;
        }
    }
