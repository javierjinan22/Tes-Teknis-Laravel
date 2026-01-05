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
    protected $description = 'Fetch Pokemon data from PokeAPI (ID 1-400, weight >= 100)';

    public function handle()
    {
        $this->info('Starting Pokemon data fetch...');
        
        $savedCount = 0;
        $skippedCount = 0;
        
        for ($id = 1; $id <= 400; $id++) {
            try {
                $this->info("Fetching Pokemon ID: {$id}");
                
                $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");
                
                if ($response->failed()) {
                    $this->error("Failed to fetch Pokemon ID: {$id}");
                    continue;
                }
                
                $pokemonData = $response->json();
                
                if ($pokemonData['weight'] < 100) {
                    $this->warn("Skipped Pokemon {$pokemonData['name']} (weight: {$pokemonData['weight']})");
                    $skippedCount++;
                    continue;
                }
                
                $imagePath = $this->downloadImage($pokemonData['sprites']['front_default'], $id);
                
                $pokemon = Pokemon::updateOrCreate(
                    ['pokemon_id' => $id],
                    [
                        'name' => $pokemonData['name'],
                        'base_experience' => $pokemonData['base_experience'] ?? 0, // ← TAMBAHKAN INI
                        'weight' => $pokemonData['weight'],
                        'image_path' => $imagePath
                    ]
                );
                
                $this->processAbilities($pokemon, $pokemonData['abilities']);
                
                $this->info("Saved Pokemon: {$pokemonData['name']} (weight: {$pokemonData['weight']}, exp: {$pokemon->base_experience})");
                $savedCount++;
                
                usleep(100000); 
                
            } catch (\Exception $e) {
                $this->error("Error processing Pokemon ID {$id}: " . $e->getMessage());
                continue;
            }
        }
        
        $this->info("\n=== Fetch Complete ===");
        $this->info("Total saved: {$savedCount}");
        $this->info("Total skipped: {$skippedCount}");
        
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
            $this->error("Failed to download image for Pokemon ID {$pokemonId}: " . $e->getMessage());
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