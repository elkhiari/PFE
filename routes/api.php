<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\VilleController;
use App\Http\Controllers\AnnonceController;
use App\Http\Middleware\AdminMiddleware;

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class,'login']);
    Route::get('/me', [AuthController::class,'me'])->middleware('auth:api');
});

Route::prefix('/users')->group(function() {
    Route::get('/', [AuthController::class, 'index']);
    Route::get('/{id}', [AuthController::class, 'show']);
    Route::middleware(['auth:api',AdminMiddleware::class])->group(function(){
        Route::put('/{id}', [AuthController::class, 'update']);
        Route::delete('/{id}', [AuthController::class, 'destroy']);
    });
})

Route::prefix('/categories')->group(function(){
    Route::get('/', [CategorieController::class, 'index']);
    Route::get('/{id}', [CategorieController::class, 'show']);
    Route::middleware(['auth:api',AdminMiddleware::class])->group(function(){
        Route::post('/', [CategorieController::class, 'store']);
        Route::put('/{id}', [CategorieController::class, 'update']);
        Route::delete('/{id}', [CategorieController::class, 'destroy']);
    });
});

Route::prefix('/villes')->group(function(){
    Route::get('/', [VilleController::class, 'index']);
    Route::get('/{id}', [VilleController::class, 'show']);
    Route::middleware(['auth:api',AdminMiddleware::class])->group(function(){
        Route::post('/', [VilleController::class, 'store']);
        Route::put('/{id}', [VilleController::class, 'update']);
        Route::delete('/{id}', [VilleController::class, 'destroy']);
    });
});


Route::prefix('/annonces')->group(function(){
    Route::get('/', [AnnonceController::class, 'index']);
    Route::get('/{id}', [AnnonceController::class, 'show']);
    Route::middleware(['auth:api'])->group(function(){
        Route::post('/', [AnnonceController::class, 'store']);
        Route::put('/validate/{id}', [AnnonceController::class, 'setVlidateTrue']);
        Route::get('/validate/false', [AnnonceController::class, 'getvalidateFalse']);
        Route::put('/{id}', [AnnonceController::class, 'update']);
        Route::delete('/{id}', [AnnonceController::class, 'destroy']);
    });
});