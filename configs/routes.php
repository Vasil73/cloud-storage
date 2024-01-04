<?php

    use Controllers\AdminController;
use Controllers\AuthController;
use Controllers\FileAccessController;
    use Controllers\FilesController;
    use Controllers\FoldersFileController;
    use Controllers\UserController;

    include_once ('index.php');

    $urlList = [
        'GET' => [
            '/users/list/' => [UserController::class, 'getUsers'],
            '/user/get/{id}/' => [UserController::class, 'getUserId'],
            '/users/logout/{id}/' => [AuthController::class, 'logout'],
            '/admin/users/list/' => [AdminController::class, 'getUserList'],
            '/admin/users/get/{id}/' => [AdminController::class, 'getUserById'],
            '/files/list/' => [FilesController::class, 'fileList'],
            '/files/get/{id}/' => [FilesController::class, 'getFile'],
            '/directories/get/{id}/' => [FoldersFileController::class, 'getFolder'],
            '/user/search/{email}/' => [UserController::class, 'searchByEmail'],
            '/get/shared/user/' => [FileAccessController::class, 'getSharedUsers']
        ],
        'POST' => [
            '/user/create/' => [UserController::class, 'createUser'],
            '/users/login/' => [AuthController::class, 'handleLoginRequest'],
            '/users/reset_password/' => [AuthController::class, 'resetPassword'],
            '/files/add/' => [FilesController::class, 'addFile'],
            '/directories/add/' => [FoldersFileController::class, 'addFolder'],
            '/users/register/' => [AuthController::class, 'register'],
        ],
        'PUT' => [
            '/user/update/{id}/' => [UserController::class, 'updateUser'],
            '/admin/users/update/{id}/' => [AdminController::class, 'updateUser'],
            '/files/rename/{id:\d+}/' => [FilesController::class, 'renameFile'],
            '/directories/rename/{id:\d+}/' => [FoldersFileController::class, 'renameFolder'],
            '/files/share/{id:\d+}/{user_id:\d+}/' => [FileAccessController::class, 'addSharedUsers']
        ],
        'DELETE' => [
            '/admin/users/delete/{id:\d+}/' => [AdminController::class, 'deleteUsers'],
            '/user/delete/{id}/' => [UserController::class, 'deleteUser'],
            '/files/remove/{id:\d+}/' => [FilesController::class, 'removeFile'],
            '/directories/delete/{id:\d+}/' => [FoldersFileController::class, 'removeFolder'],
            '/files/share/{id:\d+}/{user_id:\d+}/' => [FileAccessController::class, 'removeSharedUser']
        ]
    ];
