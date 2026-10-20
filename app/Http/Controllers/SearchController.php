<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Artist;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Limpieza del texto que se ingresa en el buscador
        $query = strtolower(trim($request->input('query', '')));

        if (empty($query)) {
            return response()->json(['songs' => [], 'artists' => []]);
        }

        // Procesar el título de la canción a minúsculas en la consulta
        $songs = Song::with(['album.artist'])
            ->whereRaw('LOWER(title) LIKE ?', ["%{$query}%"])
            ->take(10)
            ->get();

        // Procesar el nombre del artista a minúsculas en la consulta
        $artists = Artist::whereRaw('LOWER(stage_name) LIKE ?', ["%{$query}%"])
            ->take(10)
            ->get();

return response()->json([
    'songs' => $songs,
    'artists' => $artists
]);
    }
}