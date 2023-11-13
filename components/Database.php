<?php

namespace components;

    use PDO;
    use PDOException;

    class Database
    {
        private static ?PDO $pdoInstance = null;

        private function __construct()
        {
            //Сделайте конструктор закрытым, чтобы обеспечить соблюдение шаблона Singleton.
        }

        public static function getInstance(): PDO
        {
            if (self::$pdoInstance === null) {
                try {
                    $dsn = 'mysql:host=localhost:3306;dbname=cloud_storage;charset=utf8';
                    $username = 'root';
                    $password = '';

                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ];

                    self::$pdoInstance = new PDO($dsn, $username, $password, $options);
                } catch (PDOException $e) {
                    die('Database connection failed: ' . $e->getMessage());
                }
            }

            return self::$pdoInstance;
        }
    }

