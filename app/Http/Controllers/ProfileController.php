<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ProfileController extends Controller
{
    public function requestArtistAccount(Request $request)
    {
        // 1. Validar que envíen el nombre artístico
        $request->validate([
            'stage_name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $stageName = $request->input('stage_name');

        // Buscar si el nombre artístico ya existe en la base de datos
        $artist = Artist::where('stage_name', $stageName)->first();

        if ($artist) {
            // Escenario A: El nombre existe y ya tiene un dueño (user_id no es null)
            if ($artist->user_id !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este nombre artístico ya está registrado por otro usuario.',
                    'errors' => ['stage_name' => ['El nombre ya está en uso.']]
                ], 422); 
            }

            // Escenario B: El nombre existe pero está libre (user_id es null). Se reclama.
            $artist->update([
                'user_id' => $user->id,
                'verification_status' => 'pending',
                'rejection_reason' => null // Limpiamos el motivo si fue rechazado antes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada correctamente. Has reclamado este nombre artístico.'
            ], 200);
        }

        // Escenario C: El nombre no existe. Se crea una nueva solicitud desde cero.
        Artist::create([
            'user_id' => $user->id,
            'stage_name' => $stageName,
            'verification_status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud enviada correctamente. El administrador la revisará.'
        ], 201);
    }
}

