<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\ArtistRequestController;

// rutas publicas
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/search', [SearchController::class, 'index']);


// estas rutas si requieren autenticacion
Route::middleware('auth:sanctum')->group(function () {
    // gestion del peril del usuario
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'update']);
    Route::delete('/user', [UserController::class, 'destroy']);

    // gestion de canciones
    Route::get('/songs', [SongController::class, 'index']);
    Route::post('/songs', [SongController::class, 'store']);

    // creacion de artista
    Route::post('/artists', [ArtistController::class, 'store']);

    // creacion de albums
    Route::post('/albums', [AlbumController::class, 'store']);

    // panel del admin donde puede aprobar o rechazar solicitudse de ser artista
    Route::prefix('admin')->middleware('is_admin')->group(function () {
        Route::get('/artist-requests', [ArtistRequestController::class, 'index']);
        Route::put('/artist-requests/{artist}/approve', [ArtistRequestController::class, 'approve']);
        Route::delete('/artist-requests/{artist}/reject', [ArtistRequestController::class, 'reject']);
    });
});