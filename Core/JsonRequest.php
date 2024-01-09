<?php

namespace Core;

use JsonException;

    class JsonRequest
    {
        private array $data;

        /**
         * @throws JsonException
         */
        public function __construct()
        {
            $json = file_get_contents('php://input');
            $this->data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new JsonException("Ошибка разбора JSON: " . json_last_error_msg());
            }
//            if (!is_array($data)) {
//                throw new JsonException("JSON должен быть массивом");
//            }
//            $this->data = $data;
        }

        public function getData(): array
        {
            return $this->data;
        }

        public function get(string $key, $default = null)
        {
            return $this->data[$key] ?? $default;
        }

    }
