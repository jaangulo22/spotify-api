<?php

namespace App\Http\Controllers;

use App\Http\Resources\SongResource;
use App\Models\PlayHistory;
use App\Models\Song;
use Illuminate\Http\Request;


class PlayHistoryController extends Controller
{
    // obtenemos el historual de reproducciones del usuario
    public function index(Request $request)
    {

        $histories = PlayHistory::with(['song.album.artist', 'song.album.genre'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('played_at')
            ->take(20)
            ->get();

        $data = $histories->map(function ($item) {
            return [
                'played_at' => $item->played_at,
                'song' => new SongResource($item->song),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);

    }

    // guardamos o actualizamos el historial de reproduccion del usuario
    public function store(Request $request, Song $song)
    {
        
        $history = PlayHistory::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'song_id' => $song->id,
            ],
            [
                'played_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Reproducción registrada en el historial',
            'data' => [
                'song_id' => $history->song_id,
                'played_at' => $history->played_at,
            ],
        ], 201);

    }

}