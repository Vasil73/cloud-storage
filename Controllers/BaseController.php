<?php

namespace Controllers;

use Core\JsonRequest;
use Core\Response;
use Core\SessionManager;
use Exception;
    
    abstract class BaseController
    {
      //  protected mixed $model;
        protected JsonRequest $jsonRequest;
        protected SessionManager $sessionManager;

        public function __construct() // $modelClassName
        {
//            $modelName = "\\Models\\" . $modelClassName;
//            $this->model = new $modelName();
            $this->jsonRequest = new JsonRequest();
            $this->sessionManager = new SessionManager();
        }
    
        protected function sendResponse($data, int $statusCode = 200): void
        {
            try {
                Response::sendJsonResponse ( $data, $statusCode );
            } catch (Exception $e) {
                $this->handleException ( $e );
            }
        }
    
        protected function handleException(Exception $e): void
        {
            $statusCode = $e->getCode ();
            $statusCode = ($statusCode >= 100 && $statusCode <= 599) ? $statusCode : 500;
            Response::sendJsonResponse ( ['error' => $e->getMessage ()], $statusCode );
        }
    }
