<?php

namespace Core;

    class Router {
        private $routes;

        public function __construct($routes)
        {
            $this->routes = $routes;
        }

        public function route(Request $request)
        {
            foreach($this->routes as $method => $routes) {
                if ($method === $request->getMethod()) {
                    foreach($routes as $route => $action) {
                        $matchResult = $this->matchRoute($route, $request->getUri());
                        if ($matchResult['isMatch']) {
                            $controller = new $action[0]();  // $controller->{$action[1]}($matchResult['params']
                            $controller->{$action[1]}($matchResult['params']);
                        }
                    }
                }
            }
            Response::sendResponse('Not Found', 404);
            return false;
        }

        private function matchRoute(string $route, string $uri): array
        {
            $routeParts = explode('/', trim($route, '/'));
            $uriParts = explode('/', trim($uri, '/'));
            $params = [];
            if (count($routeParts) === count($uriParts)) {
                for ($i = 0; $i < count($routeParts); $i++) {
                    if ($this->isParam($routeParts[$i])) {
                        $params[$this->getParamName($routeParts[$i])] = $uriParts[$i];
                    } else if ($routeParts[$i] !== $uriParts[$i]) {
                        return ['isMatch' => false];
                    }
                }
                return ['isMatch' => true, 'params' => $params];
            }
            return ['isMatch' => false];
        }

        private function isParam(string $routePart): bool
        {
            return preg_match('/\{.*\}/', $routePart) === 1;
        }

        private function getParamName(string $routePart): string
        {
            return trim($routePart, '{}');
        }
    }