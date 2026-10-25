<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ArtistRequestController extends Controller
{
    // lista de solicitudes pendientes
    public function index()
    {
        $requests = Artist::with('user')
            ->where('verification_status', 'pending')
            ->get();

        return response()->json([
            'success' => true, 
            'data' => $requests
        ], 200);
    }


    public function approve(Artist $artist)
    {
        if ($artist->verification_status !== 'pending') {
            return response()->json([
                'success' => false, 
                'message' => 'Esta solicitud ya fue procesada o no es válida.'
            ], 400);
        }

        DB::transaction(function () use ($artist) {

            $artist->update(['verification_status' => 'verified']);
            
            // buscar ek rol del usuario
            $role = DB::table('roles')->where('name', 'Artista')->first(); 

            if ($role) {
                $artist->user->update(['role_id' => $role->id]); 
            }

        });

        return response()->json([
            'success' => true, 
            'message' => 'Artista aprobado. El usuario ya puede subir música.'
        ], 200);

    }

    // denegar la solicitud
    public function reject(Artist $artist)
    {
        // eliminr la solicitud
        $artist->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Solicitud rechazada y eliminada.'
        ], 200);
    }


}