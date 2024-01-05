<?php

declare(strict_types=1);

namespace Controllers;

    use Core\JsonRequest;
    use Core\Response;
    use Core\SessionManager;
    use Exception;
    use JsonException;
    use Models\AuthModel;
    use PDOException;

    class AuthController
    {
        private AuthModel $authModel;
        private SessionManager $sessionManager;

        public function __construct()
        {
            $this->authModel = new AuthModel();
            $this->sessionManager = new SessionManager();
            $this->sessionManager->start();
        }

        public function register(): void
        {
            $input = new JsonRequest();
            $data = $input->getData ();

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                Response::sendJsonResponse(["error" => 'Неправильный JSON'], 400);
                return; // Завершаем выполнение скрипта
            }

            if (isset($data['name'], $data['email'], $data['password'], $data['role'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $registerResult = $this->authModel->register($data['name'], $data['email'], $hashedPassword, $data['role']);
                if ($registerResult) {
                    Response::sendJsonResponse(["message" => 'Вы успешно зарегистрировались'], 400);
                   exit(); // Завершаем выполнение скрипта
                } elseif (!$registerResult) {
                    Response::sendJsonResponse (["error" => "Ошибка регистрации"]);
                }
            } else {
                Response::sendJsonResponse(["error" => 'Данные не полные'], 400);
                exit(); // Завершаем выполнение скрипта
            }
        }

        public function login($email, $password)
        {
            try {
                $user = $this->authModel->authenticate($email);
                if (!$user) {
                    return false;
                }

                $hashedPassword = $user['password'];
                if (password_verify($password, $hashedPassword)) {
                    $token = $this->generateToken();
                    $this->authModel->setToken($user['id'], $token);
                    $this->sessionManager->set('user_id', $user['id']);
                    $this->sessionManager->set('token', $token);

                    setcookie ('session_token', $token, time () + 3600, '/', '', true, true);

                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $ex)
            {
                error_log($ex->getMessage()); // Логирование ошибки
                Response::sendJsonResponse(["error" => "Внутренняя ошибка сервера"], 500);
                exit();
            }
        }

         public function logout($id): void
         {
             try {
                 $this->authModel->logout($id);

                     $this->sessionManager->destroy();
                     Response::sendJsonResponse(["message" => "Вы успешно вышли из системы"], 200);
                     return;
             } catch (PDOException $ex) {
                 Response::sendJsonResponse(["error" => "Ошибка при выходе из системы"], 400);
             } catch (Exception $e) {
                 Response::sendJsonResponse ( ["error" => "Внутренняя ошибка сервера"], 500 );
             }
         }

        public function resetPassword()
        {
            $requestBody = new JsonRequest();
            $requestData = $requestBody->getData ();

            $email = $requestData['email'];

            $resetStatus = $this->authModel->resetPassword($email);
            if ($resetStatus) {
                Response::sendJsonResponse([
                    'status' => 'success',
                    'message' => 'Проверьте вашу электронную почту для дальнейших инструкций по сбросу пароля'
                ], 200);
            } else {
                Response::sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Возникла ошибка при попытке сбросить ваш пароль'
                ], 400);
            }
        }

        private function generateToken(): string
        {
            return bin2hex(openssl_random_pseudo_bytes(16));
        }

        public function handleLoginRequest(): void
        {
            $jsonData = new JsonRequest();
            $data = $jsonData->getData ();

            if (isset($data['email']) && isset($data['password'])) {
                $result = $this->login($data['email'], $data['password']);
                if ($result) {
                    Response::sendJsonResponse(["message" => "Успешный вход в систему"], 200);
                    exit();
                } else {
                    Response::sendJsonResponse(["error" => "Неверные учетные данные"], 401);
                }
            } else {
                Response::sendJsonResponse(["error" => "Отсутствуют необходимые данные"], 400);
            }
        }

    }
