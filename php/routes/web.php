<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    throw new LogicException();
    return "hello";
})->name("home");
