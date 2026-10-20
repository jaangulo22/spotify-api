<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\SearchController;

Route::post('/login', [LoginController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);

Route::get('/songs', [SongController::class, 'index']);
Route::post('/songs', [SongController::class, 'store']);

Route::post('/albums', [AlbumController::class, 'store']);

Route::post('/artists', [ArtistController::class, 'store']);

Route::get('/search', [SearchController::class, 'index']);