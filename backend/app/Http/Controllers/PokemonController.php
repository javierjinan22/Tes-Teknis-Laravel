<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function index()
    {
        $pokemons = Pokemon::with('abilities')
            ->orderBy('weight', 'desc')
            ->get();
        
        return response()->json($pokemons);
    }
    
    public function show($id)
    {
        $pokemon = Pokemon::with('abilities')->findOrFail($id);
        return response()->json($pokemon);
    }
}