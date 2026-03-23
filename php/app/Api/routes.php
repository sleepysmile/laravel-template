<?php

use App\Api\Controllers\AuthController;
use App\Api\Controllers\UserController;
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
        Route::post("/user", [UserController::class, "update"]);
    });
});
