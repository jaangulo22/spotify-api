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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ArtistRequestController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PlaylistController;
use App\Models\Genre;
use App\Http\Controllers\PlayHistoryController;

// rutas publicas
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/search', [SearchController::class, 'index']);

//Obtener Genneros
Route::get('/genres', function () {
return response()->json([
    'data' => Genre::select('id', 'name')->orderBy('name')->get()
    ]);
});

// estas rutas si requieren autenticacion
Route::middleware('auth:sanctum')->group(function () {
    // gestion del peril del usuario
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'update']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::delete('/user', [UserController::class, 'destroy']);

    // gestion de canciones
    Route::get('/songs', [SongController::class, 'index']);
    Route::post('/songs', [SongController::class, 'store']);

    Route::get('/songs/{song}', [SongController::class, 'show']);
    Route::put('/songs/{song}', [SongController::class, 'update']);
    Route::delete('/songs/{song}', [SongController::class, 'destroy']);

    // creacion de artista
    Route::post('/artists', [ArtistController::class, 'store']);
    Route::get('/artists/{artist}', [ArtistController::class, 'show']);
    Route::put('/artists/{artist}', [ArtistController::class, 'update']);
    Route::delete('/artists/{artist}', [ArtistController::class, 'destroy']);

    // creacin de solicitud para perfil de artista
    Route::post('/artist-request', [ProfileController::class, 'requestArtistAccount']);

    // creacion de albums
    Route::get('/albums', [AlbumController::class, 'index']);
    Route::post('/albums', [AlbumController::class, 'store']);

    // gestion de favoritos (like o me gusta)
    Route::get('/favorites/songs', [FavoriteController::class, 'index']);
    Route::post('/favorites/songs/{song}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/songs/{song}', [FavoriteController::class, 'destroy']);

    // gestión de playlists
    Route::get('/playlists', [PlaylistController::class, 'index']);
    Route::post('/playlists', [PlaylistController::class, 'store']);
    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show']);
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy']);

    // agregar y quitar canciones de una playlist
    Route::post('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'addSong']);
    Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong']);

    // historial de reproducciones
    Route::get('/history', [PlayHistoryController::class, 'index']);
    Route::post('/history/{song}', [PlayHistoryController::class, 'store']);
    
    // panel del admin donde puede aprobar o rechazar solicitudse de ser artista
    Route::prefix('admin')->middleware('is_admin')->group(function () {
        Route::get('/artist-requests', [ArtistRequestController::class, 'index']);
        Route::put('/artist-requests/{artist}/approve', [ArtistRequestController::class, 'approve']);
        Route::patch('/artist-requests/{artist}/reject', [ArtistRequestController::class, 'reject']);
    });
});