<?php

namespace App\Console\Commands;

use App\Models\Pokemon;
use App\Models\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchPokemonData extends Command
{
    protected $signature = 'pokemon:fetch';
    protected $description = 'Fetch Pokemon data from PokeAPI';

    public function handle()
    {
        $savedCount = 0;
        
        for ($id = 1; $id <= 400; $id++) {
            try {
                $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");
                
                if ($response->failed()) {
                    continue;
                }
                
                $pokemonData = $response->json();
                
                if ($pokemonData['weight'] < 100) {
                    continue;
                }
                
                $imagePath = $this->downloadImage($pokemonData['sprites']['front_default'], $id);
                
                $pokemon = Pokemon::updateOrCreate(
                    ['pokemon_id' => $id],
                    [
                        'name' => $pokemonData['name'],
                        'base_experience' => $pokemonData['base_experience'] ?? 0,
                        'weight' => $pokemonData['weight'],
                        'image_path' => $imagePath
                    ]
                );
                
                $this->processAbilities($pokemon, $pokemonData['abilities']);
                
                $savedCount++;
                
                usleep(100000); 
                
            } catch (\Exception $e) {
                continue;
            }
        }
        
        $this->info("Selesai! Total Pokemon tersimpan: {$savedCount}");
        
        return Command::SUCCESS;
    }
    
    private function downloadImage($imageUrl, $pokemonId)
    {
        if (!$imageUrl) {
            return null;
        }
        
        try {
            $directory = storage_path('app/public/pokemon_images');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $imageContent = file_get_contents($imageUrl);
            $filename = "pokemon_{$pokemonId}.png";
            $filepath = $directory . '/' . $filename;
            
            file_put_contents($filepath, $imageContent);
            
            return $filepath;
            
        } catch (\Exception $e) {
            return null;
        }
    }
    
    private function processAbilities($pokemon, $abilitiesData)
    {
        $abilityIds = [];
        
        foreach ($abilitiesData as $abilityData) {
            if ($abilityData['is_hidden']) {
                continue;
            }
            
            $ability = Ability::firstOrCreate([
                'name' => $abilityData['ability']['name']
            ]);
            
            $abilityIds[] = $ability->id;
        }
        
        $pokemon->abilities()->sync($abilityIds);
    }
}