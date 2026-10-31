<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use App\Http\Resources\ArtistResource;
use Illuminate\Validation\Rule;

class ArtistController extends Controller
{
    public function store(Request $request)
    {
        // evitar que un usuario artista o en proceso del mismo, que no pueda hacer mas solicitudes
        if ($request->user()->artist) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes una solicitud en proceso o ya eres artista.'
            ], 403);
        }

        $validated = $request->validate([
            'stage_name' => ['required', 'string', 'max:255', Rule::unique('artists', 'stage_name')],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['verification_status'] = 'pending';

        $artist = Artist::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Artista creado exitosamente',
            'data' => new ArtistResource($artist)
        ], 201);
    }

    // ver informacion del artista
    public function show(Artist $artist)
    {
        return response()->json([
            'success' => true,
            'data' => new ArtistResource($artist)
        ]);
    }

    // actualizar informacion del artista
    public function update(Request $request, Artist $artist)
    {
        // con esti se verifica que el usuario que quiere actualizar sea este perfil
        if ($request->user()->id !== $artist->user_id) {
            return response()->json(['message' => 'No tienes permiso para editar este perfil'], 403);
        }

        $validated = $request->validate([
            'stage_name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('artists')->ignore($artist->id)],
        ]);

        $artist->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Perfil de artista actualizado',
            'data' => new ArtistResource($artist)
        ]);
    }

    // eliminar perfil de artista
    public function destroy(Request $request, Artist $artist)
    {
        // igual que el update, verificamos que sea el usuario de este perfil
        if ($request->user()->id !== $artist->user_id) {
            return response()->json(['message' => 'No tienes permiso para eliminar este perfil'], 403);
        }

        $artist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perfil de artista eliminado correctamente'
        ]);
    }

}