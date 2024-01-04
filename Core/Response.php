<?php

namespace Core;

    class Response
    {
        public static function sendResponse($content, $code = 200)
        {
            http_response_code($code);
            echo $content;
            exit;
        }

        public static function sendJsonResponse($content, $code = 200)
        {
            header('Content-Type: application/json');
            http_response_code($code);
            echo json_encode($content);
            exit;
        }
    }