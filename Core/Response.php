<?php

namespace Core;

    class Response
    {
        public static function sendJson(array $data, int $statusCode = 200)
        {
            http_response_code($statusCode);
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }

        public static function sendError(string $message, int $statusCode = 500)
        {
            self::sendJson(['error' => $message], $statusCode);
        }
    }

