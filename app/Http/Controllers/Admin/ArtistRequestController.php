<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ArtistRequestController extends Controller
{
    // Lista de solicitudes pendientes (solo con user_id activo)
    public function index()
    {
        $requests = Artist::with('user')
            ->where('verification_status', 'pending')
            ->whereNotNull('user_id')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $requests,
        ], 200);
    }

    // Aprobar la solicitud: pasa a verified y asigna rol Artista
    public function approve(Artist $artist)
    {
        if ($artist->verification_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Esta solicitud ya fue procesada o no es válida.',
            ], 400);
        }

        DB::transaction(function () use ($artist) {
            $artist->update(['verification_status' => 'verified']);

            $role = DB::table('roles')->where('name', 'Artista')->first();

            if ($role && $artist->user) {
                $artist->user->update(['role_id' => $role->id]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Artista aprobado. El usuario ya puede subir música.',
        ], 200);
    }

    // Rechazar la solicitud: libera el stage_name (user_id = null) y marca como rejected
    public function reject(Request $request, Artist $artist)
    {
        $reason = $request->input('rejection_reason', 'Solicitud no aprobada por el administrador.');

        $artist->update([
            'user_id'             => null,
            'verification_status' => 'rejected',
            'rejection_reason'    => $reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud rechazada. El nombre artístico ha quedado libre.',
        ], 200);
    }
}
