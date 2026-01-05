<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    protected $table = 'pokemons'; 

    protected $fillable = [
        'pokemon_id',
        'name',
        'base_experience',
        'weight',
        'image_path'
    ];

    protected $casts = [
        'pokemon_id' => 'integer',
        'base_experience' => 'integer',
        'weight' => 'integer'
    ];

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'pokemon_abilities');
    }
}