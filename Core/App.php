<?php

namespace Core;

    use InvalidArgumentException;
    use LogicException;
    use OutOfBoundsException;

    class App
    {
        private static array $services = [];

        public static function addService($name, $service)
        {
            if (!is_string($name)) {
                throw new InvalidArgumentException("Имя сервиса должно быть строкой.");
            }
            if (empty($name)) {
                throw new InvalidArgumentException("Имя сервиса не может быть пустым.");
            }
            if (isset(self::$services[$name])) {
                throw new LogicException("Сервис уже зарегистрирован.");
            }
            self::$services[$name] = $service;
        }

        public static function getService($name)
        {
            if (!is_string($name)) {
                throw new InvalidArgumentException("Имя сервиса должно быть строкой.");
            }
            if (!isset(self::$services[$name])) {
                throw new OutOfBoundsException("Сервис с именем '$name' не найден.");
            }
            return self::$services[$name];
        }

        public static function hasService($name)
        {
            return isset(self::$services[$name]);
        }

        public static function removeService($name)
        {
            if (!is_string($name)) {
                throw new InvalidArgumentException("Имя сервиса должно быть строкой.");
            }
            if (!isset(self::$services[$name])) {
                throw new OutOfBoundsException("Невозможно удалить сервис: сервис с именем '$name' не найден.");
            }
            unset(self::$services[$name]);
        }

        public static function clearServices()
        {
            self::$services = [];
        }
    }
