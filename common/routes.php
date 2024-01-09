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
            '/users/admin/list/' => [AdminController::class, 'getUserList'],
            '/users/admin/get/{id}/' => [AdminController::class, 'getUserById'],
            '/files/list/' => [FilesController::class, 'listFiles'],
            '/files/get/{id}/' => [FilesController::class, 'getFile'],
            '/files/directories/get/{id}/' => [FoldersFileController::class, 'getFolderId'],
            '/user/search/{email}/' => [UserController::class, 'searchByEmail'],
            '/user/get/shared/' => [FileAccessController::class, 'getSharedUsers']
        ],
        'POST' => [

            '/user/create/' => [UserController::class, 'createUser'],
            '/users/login/' => [AuthController::class, 'handleLoginRequest'],
            '/users/reset_password/' => [AuthController::class, 'resetPassword'],
            '/files/add/' => [FilesController::class, 'addFile'],
            '/files/directories/add/' => [FoldersFileController::class, 'addFolder'],
            '/users/register/' => [AuthController::class, 'register'],
        ],
        'PUT' => [
            '/user/update/{id}/' => [UserController::class, 'updateUser'],
            '/user/admin/update/{id}/' => [AdminController::class, 'updateUserAdmin'],
            '/files/rename/{id}/' => [FilesController::class, 'renameFile'],
            '/files/directories/rename/{id}/' => [FoldersFileController::class, 'renameFolder'],
            '/files/share/{id}/{user_id}/' => [FileAccessController::class, 'addSharedUsers']
        ],
        'DELETE' => [
            '/users/admin/delete/{id}/' => [AdminController::class, 'deleteUser'],
            '/user/delete/{id}/' => [UserController::class, 'deleteUser'],
            '/files/remove/{id}/' => [FilesController::class, 'removeFile'],
            '/files/directories/delete/{id}/' => [FoldersFileController::class, 'removeFolder'],
            '/files/share_delete/{id}/{user_id}/' => [FileAccessController::class, 'removeSharedUser']
        ]
    ];
