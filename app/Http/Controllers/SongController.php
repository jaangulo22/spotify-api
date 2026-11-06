<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    public function index()
    {
        $songs = Song::with('album.artist', 'album.genre')->get();

        return response()->json([
            'success' => true,
            'data' => SongResource::collection($songs)
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'album_id'         => 'required|uuid|exists:albums,id',
            'title'            => 'required|string|max:255',
            'duration_seconds' => 'nullable|integer',
            'audio_url'        => 'nullable|string',
            'audio_file'       => 'nullable|file|mimes:mp3,wav,ogg,mpga,m4a|max:51200', // max 50MB
            'track_number'     => 'nullable|integer',
        ]);

        // Si se subió un archivo físico de audio
        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('songs', 'public');
            $validated['audio_url'] = url(Storage::url($path));
        }

        if (empty($validated['audio_url'])) {
            return response()->json([
                'success' => false,
                'message' => 'Debes proporcionar un archivo de audio o una URL.',
                'errors'  => ['audio_url' => ['El audio es requerido.']]
            ], 422);
        }

        // Si duration_seconds no viene o es 0, poner 0 por defecto para evitar fallo de integridad
        $validated['duration_seconds'] = !empty($validated['duration_seconds']) 
            ? (int) $validated['duration_seconds'] 
            : 0;

        // Calcular track_number automático si no se proporcionó
        if (empty($validated['track_number'])) {
            $lastTrack = Song::where('album_id', $validated['album_id'])->max('track_number');
            $validated['track_number'] = ($lastTrack ?? 0) + 1;
        }

        unset($validated['audio_file']);

        $song = Song::create($validated);
        $song->load('album.artist', 'album.genre');

        return response()->json([
            'success' => true,
            'message' => 'Canción creada exitosamente',
            'data' => new SongResource($song)
        ], 201);
    }

    // obtener la info de una cancion especifica
    public function show(Song $song)
    {
        return response()->json([
            'success' => true,
            'data' => new SongResource($song)
        ]);
    }

    public function update(Request $request, Song $song)
    {
        // es dueño de la cancion o admin?
        $ownerId = $song->album->artist->user_id;
        
        if ($request->user()->id !== $ownerId && $request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar esta canción.'
            ], 403);
        }

        $validated = $request->validate([
            'album_id'         => 'sometimes|required|uuid|exists:albums,id',
            'title'            => 'sometimes|required|string|max:255',
            'duration_seconds' => 'sometimes|required|integer',
            'audio_url'        => 'sometimes|required|string',
            'track_number'     => 'nullable|integer',
        ]);

        $song->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Canción actualizada correctamente',
            'data' => new SongResource($song)
        ], 200);
    }

    public function destroy(Request $request, Song $song)
    {
        // es dueño de la cancion o admin?
        $ownerId = $song->album->artist->user_id;
        
        if ($request->user()->id !== $ownerId && $request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta canción.'
            ], 403);
        }

        $song->delete();
        return response()->json([
            'success' => true,
            'message' => 'Canción eliminada correctamente'
        ], 200);
    }
}
