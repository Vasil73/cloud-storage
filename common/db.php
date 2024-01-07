<?php

$config = [
    'host' => 'localhost',
    'dbname' => 'my_database',
    'username' => 'user',
    'password' => 'password',
    'options' => [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]
];
