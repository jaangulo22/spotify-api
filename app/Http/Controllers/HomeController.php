<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Artist;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $recentSongs = Song::with('album.artist')->latest()->take(5)->get();
        $trendingSongs = Song::with('album.artist')->inRandomOrder()->take(5)->get();
        $forYouSongs = Song::with('album.artist')->inRandomOrder()->take(5)->get();
        $discoverySongs = Song::with('album.artist')->inRandomOrder()->take(8)->get();

        dd($recentSongs);

        return view('home', compact('recentSongs', 'trendingSongs', 'forYouSongs', 'discoverySongs'));
    }
}