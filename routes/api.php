<?php

use App\Http\Controllers\Api\Profile\CommentController;
use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');

Route::group(['middleware' => ['auth:api']], function () {
    Route::apiResource('profile', ProfileController::class)->except(['index']);
    Route::apiResource('profile/comment', CommentController::class)->only(['store']);
});
