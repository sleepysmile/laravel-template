<?php

use App\Http\Client\Ui\Controllers\AuthController;
use App\Http\Client\Ui\Controllers\ChatController;
use App\Http\Client\Ui\Controllers\MessageController;
use App\Http\Client\Ui\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    "middleware" => [
        "api"
    ]
], function () {
    Route::post("/signin", [AuthController::class, "signin"]);

    Route::group([
        "middleware" => [
            "auth:sanctum",
        ]
    ], function () {
        Route::get("/users", [UserController::class, "find"]);

        Route::group([
            "prefix" => "/chat"
        ], function () {
            Route::post("/init", [ChatController::class, "init"]);
            Route::post("/message/create", [MessageController::class, "create"]);
            Route::get("/message/pooling", [MessageController::class, "pooling"]);
            Route::get("/message/find", [MessageController::class, "find"]);
        });
    });
});
