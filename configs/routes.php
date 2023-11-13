<?php
//
//$urlList = [
//        'GET' => [
//            '/user/create/' => [UserController::class, 'createUser'],
//            '/users/list/' => [UserController::class, 'getUsers'],
//            '/users/get/{id:\d+}/' => [UserController::class, 'getUserById'],
//            '/users/logout/{id:\d+}/' => [UserController::class, 'logout'],
//            '/admin/users/list/' => [AdminController::class, 'getUserList'],
//            '/admin/users/get/{id:\d+}/' => [AdminController::class, 'getUserById'],
//            '/files/list/' => [FilesController::class, 'fileList'],
//            '/files/get/{id:\d+}/' => [FilesController::class, 'getFile'],
//            '/directories/get/{id:\d+}/' => [FoldersFileController::class, 'getFolder'],
//            '/user/search/{email}/' => [UserController::class, 'searchByEmail'],
//            '/get/shared/user/' => [FileAccessController::class, 'getSharedUsers']
//        ],
//        'POST' => [
//            '/users/login/' => [UserController::class, 'login'],
//            '/users/reset_password' => [UserController::class, 'resetPassword'],
//            '/files/add/' => [FilesController::class, 'addFile'],
//            '/directories/add/' => [FoldersFileController::class, 'addFolder'],
//            '/users/register/' => [UserController::class, 'register']
//        ],
//        'PUT' => [
//            '/users/update/{id:\d+}' => [UserController::class, 'updateUser'],
//            '/admin/users/update/{id:\d+}' => [AdminController::class, 'updateUser'],
//            '/files/rename/{id:\d+}' => [FilesController::class, 'renameFile'],
//            '/directories/rename/{id:\d+}' => [FoldersFileController::class, 'renameFolder'],
//            '/files/share/{id:\d+}/{user_id:\d+}' => [FileAccessController::class, 'addSharedUsers']
//        ],
//        'DELETE' => [
//            '/admin/users/delete/{id:\d+}' => [AdminController::class, 'deleteUsers'],
//            '/user/delete/{id:\d+}' => [UserController::class, 'deleteUser'],
//            '/files/remove/{id:\d+}' => [FilesController::class, 'removeFile'],
//            '/directories/delete/{id:\d+}' => [FoldersFileController::class, 'removeFolder'],
//            '/files/share/{id:\d+}/{user_id:\d+}' => [FileAccessController::class, 'removeSharedUser']
//        ]
//    ];