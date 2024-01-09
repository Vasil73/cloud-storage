<?php

    return [
        'host' => 'localhost',
        'dbname' => 'cloud_storage',
        'username' => 'root',
        'password' => '',
        'options' => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]
    ];
