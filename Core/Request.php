<?php

namespace Core;

    class Request
    {
        private mixed $method;
        private string|false $uri;

        public function __construct()
        {
            $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $this->uri = strtok($_SERVER["REQUEST_URI"] ?? '/', '?');
        }

        public function getMethod()
        {
            return $this->method;
        }

        public function getUri()
        {
            return $this->uri;
        }
    }