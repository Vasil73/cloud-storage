<?php

//    spl_autoload_register (function ($class)
//    {
//        $dirs = ['components', 'configs', 'Controllers', 'Models', 'Core'];
//        foreach ($dirs as $dir) {
//            $file_name = "$dir/" . mb_strtolower ($class) . ".php";
//            if (file_exists ($file_name)) {
//                require_once ($file_name);
//            }
//        }
//    });

    spl_autoload_register(function ($class) {
        $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
