<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use App\Http\Resources\AlbumResource;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    // Listar álbumes — opcionalmente filtrar por artist_id
    public function index(Request $request)
    {
        $query = Album::with('artist', 'genre');

        if ($request->filled('artist_id')) {
            $query->where('artist_id', $request->artist_id);
        }

        $albums = $query->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data'    => AlbumResource::collection($albums),
        ], 200);
    }

    // Crear un nuevo álbum
    public function store(Request $request)
    {
        $validated = $request->validate([
            'artist_id'    => 'required|uuid|exists:artists,id',
            'genre_id'     => 'required|integer|exists:genres,id',
            'title'        => 'required|string|max:255',
            'cover_url'    => 'nullable|string',
            'cover_file'   => 'nullable|image|max:10240', // max 10MB
            'release_date' => 'nullable|date',
            'album_type'   => 'required|string|max:50',
        ]);

        // Si se subió un archivo de portada local
        if ($request->hasFile('cover_file')) {
            $path = $request->file('cover_file')->store('covers', 'public');
            $validated['cover_url'] = url(Storage::url($path));
        }

        if (empty($validated['cover_url'])) {
            return response()->json([
                'success' => false,
                'message' => 'Debes proporcionar una portada (archivo o URL).',
                'errors'  => ['cover_url' => ['La portada es obligatoria.']]
            ], 422);
        }

        // Asegurar que album_type siempre esté en minúsculas para respetar el constraint de BD ('album', 'single', 'ep')
        $validated['album_type'] = strtolower(trim($validated['album_type']));

        unset($validated['cover_file']);

        $album = Album::create($validated);
        $album->load('artist', 'genre');

        return response()->json([
            'success' => true,
            'message' => 'Álbum creado exitosamente',
            'data'    => new AlbumResource($album),
        ], 201);
    }

    // Obtener un álbum específico
    public function show(Album $album)
    {
        $album->load('artist', 'genre', 'songs');

        return response()->json([
            'success' => true,
            'data'    => new AlbumResource($album),
        ], 200);
    }

    // Actualizar un álbum
    public function update(Request $request, Album $album)
    {
        $ownerId = $album->artist->user_id;

        if ($request->user()->id !== $ownerId && $request->user()->role_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este álbum.'
            ], 403);
        }

        $validated = $request->validate([
            'genre_id'     => 'sometimes|required|integer|exists:genres,id',
            'title'        => 'sometimes|required|string|max:255',
            'cover_url'    => 'nullable|string',
            'cover_file'   => 'nullable|image|max:10240',
            'release_date' => 'nullable|date',
            'album_type'   => 'sometimes|required|string|max:50',
        ]);

        if ($request->hasFile('cover_file')) {
            $path = $request->file('cover_file')->store('covers', 'public');
            $validated['cover_url'] = url(Storage::url($path));
        }

        if (isset($validated['album_type'])) {
            $validated['album_type'] = strtolower(trim($validated['album_type']));
        }

        unset($validated['cover_file']);

        $album->update($validated);
        $album->load('artist', 'genre');

        return response()->json([
            'success' => true,
            'message' => 'Álbum actualizado correctamente',
            'data'    => new AlbumResource($album),
        ], 200);
    }

    // Eliminar un álbum (sus canciones se borran por cascadeOnDelete en la BD)
    public function destroy(Request $request, Album $album)
    {
        $ownerId = $album->artist->user_id;

        if ($request->user()->id !== $ownerId && $request->user()->role_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este álbum.'
            ], 403);
        }

        $album->delete();

        return response()->json([
            'success' => true,
            'message' => 'Álbum eliminado correctamente'
        ], 200);
    }
}
