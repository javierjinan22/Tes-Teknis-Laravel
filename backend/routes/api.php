<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/pokemons', [App\Http\Controllers\PokemonController::class, 'index']);
Route::get('/pokemons/{id}', [App\Http\Controllers\PokemonController::class, 'show']);