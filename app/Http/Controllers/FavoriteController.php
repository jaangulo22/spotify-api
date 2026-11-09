<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;

class FavoriteController extends Controller
{
    // funcion pa obtener las canciones favorutas del usuario
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // se busca los favoritos del usuario
        $favorites = Song::whereHas('favoritedByUsers', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('album.artist', 'album.genre')->get();

        return response()->json([
            'success' => true,
            'data' => SongResource::collection($favorites)
        ]);
    }

    // darle me gsuta a una cancion
    public function store(Request $request, Song $song)
    {
        $userId = $request->user()->id;

        // se verifica que no lo tenga como favorita ya
        if (!$song->favoritedByUsers()->where('user_id', $userId)->exists()) {
            $song->favoritedByUsers()->attach($userId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Canción agregada a favoritos correctamente'
        ], 201);
    }

    // quitar el me gusta
    public function destroy(Request $request, Song $song)
    {
        $userId = $request->user()->id;

        // detach este es pa quitar la relacion de la tabla pivote
        $song->favoritedByUsers()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'Canción eliminada de favoritos'
        ]);
    }

}
