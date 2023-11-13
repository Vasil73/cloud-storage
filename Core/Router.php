<?php

namespace Core;

//use components\Database;
use Exception;
use Models\UserModel;

    class Router
    {
        private array $urlList;

        public function __construct(array $urlList)
        {
            $this->urlList = $urlList;
        }

        public function handleRequest()
        {
            $request = new Request();
            $requestMethod = $_SERVER[ 'REQUEST_METHOD' ];
            $requestUrl = $_SERVER[ 'REQUEST_URI' ];
            $routeFound = false;

            if (!array_key_exists($requestMethod, $this->urlList)) {
                Response::sendError('Method Not Allowed', 405);
            }

            foreach ($this->urlList[ $requestMethod ] as $url => $action) {
                $urlPattern = preg_replace('/\//', '\\/', $url);
                $urlPattern = preg_replace('/\{([a-zA-Z0-9]+):([^\}]+)\}/', '(?P<\1>\2)',  $urlPattern);
                $urlPattern = '/^' . $urlPattern . '$/';

                if (preg_match($urlPattern, $requestUrl, $matches)) {
                    $routeFound = true;
                    array_shift($matches);

                    // Removing string keys (names of capturing groups in regex) so it's all numeric
                    $paramValues = array_values($matches);
                    $controllerName = $action[0];
                    $methodName = $action[1];

                    $body = $request->getData();

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName(new UserModel());
                        if (method_exists($controller, $methodName)) {
                            try {
                                // The ID from the URL will be among the parameters
                                call_user_func_array([$controller, $methodName], $paramValues);
                                Response::sendJson([$controller->$methodName($body, $body['id'])]);
                            } catch (Exception $e) {
                                Response::sendError($e->getMessage(), 500);
                            }
                        } else {
                            Response::sendError('Method Not Found In Controller', 500);
                        }
                    } else {
                        Response::sendError('Controller Not Found', 500);
                    }
                }
            }

            if (!$routeFound) {
                Response::sendError('Page not found', 404);
            }
        }
    }
