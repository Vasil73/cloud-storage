<?php

namespace Core;

    class SessionManager
    {
        public function start()
        {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }

        public function set($key, $value)
        {
            $_SESSION[$key] = $value;
        }

        public function get($key, $default = null)
        {
            return $_SESSION[$key] ?? $default;
        }

        public function remove($key)
        {
            unset($_SESSION[$key]);
        }

        public function destroy()
        {
            session_destroy();
        }
    }
