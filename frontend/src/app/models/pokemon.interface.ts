export interface Ability {
  id: number;
  name: string;
}

export interface Pokemon {
  id: number;
  pokemon_id: number;
  name: string;
  base_experience: number;
  weight: number;
  image_path: string;
  abilities: Ability[];
  created_at?: string;
  updated_at?: string;
}

export interface PokemonResponse {
  data: Pokemon;
}

export interface PokemonsResponse {
  data: Pokemon[];
  current_page?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
}