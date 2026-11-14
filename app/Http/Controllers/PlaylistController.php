<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;

class PlaylistController extends Controller
{
    // ver todas las playlists del usuario
    public function index(Request $request)
    {
        $playlists = Playlist::where('user_id', $request->user()->id)
            ->withCount('songs')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $playlists
        ]);
    }

    // crear una playlist
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_url' => 'nullable|string',
        ]);

        $playlist = Playlist::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'cover_url' => $validated['cover_url'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Playlist creada exitosamente',
            'data' => $playlist
        ], 201);
    }

    // ver una playlist especifica
    public function show(Playlist $playlist)
    {
        $playlist->load(['songs.album.artist', 'songs.album.genre']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $playlist->id,
                'user_id' => $playlist->user_id,
                'name' => $playlist->name,
                'description' => $playlist->description,
                'cover_url' => $playlist->cover_url,
                'songs' => SongResource::collection($playlist->songs),
            ]
        ]);
    }

    // borrar una playlist
    public function destroy(Request $request, Playlist $playlist)
    {
        if ($request->user()->id !== $playlist->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta playlist.'
            ], 403);
        }

        $playlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Playlist eliminada correctamente'
        ]);
    }

    // agregar cancion a esta playlist
    public function addSong(Request $request, Playlist $playlist, Song $song)
    {
        if ($request->user()->id !== $playlist->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar esta playlist.'
            ], 403);
        }

        if (!$playlist->songs()->where('song_id', $song->id)->exists()) {
            $playlist->songs()->attach($song->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Canción agregada a la playlist'
        ], 201);
    }

    // eliminar cancion a esta playlist
    public function removeSong(Request $request, Playlist $playlist, Song $song)
    {
        if ($request->user()->id !== $playlist->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar esta playlist.'
            ], 403);
        }

        $playlist->songs()->detach($song->id);

        return response()->json([
            'success' => true,
            'message' => 'Canción eliminada de la playlist'
        ]);
    }
    
}
