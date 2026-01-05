import { Component, OnInit } from '@angular/core';
import { PokemonService } from '../../services/pokemon.service';
import { Pokemon } from '../../models/pokemon.interface';

@Component({
  selector: 'app-pokemon-list',
  templateUrl: './pokemon-list.component.html',
  styleUrls: ['./pokemon-list.component.css']
})
export class PokemonListComponent implements OnInit {
  pokemons: Pokemon[] = [];
  filteredPokemons: Pokemon[] = [];
  loading: boolean = true;
  error: string = '';
  
  selectedWeightFilter: string = 'ALL';
  
  weightFilters = [
    { label: 'All', value: 'ALL' },
    { label: 'Light (100-150)', value: 'LIGHT' },
    { label: 'Medium (151-199)', value: 'MEDIUM' },
    { label: 'Heavy (≥200)', value: 'HEAVY' }
  ];

  constructor(private pokemonService: PokemonService) { }

  ngOnInit(): void {
    this.loadPokemons();
  }

  loadPokemons(): void {
    this.loading = true;
    this.pokemonService.getPokemons().subscribe({
      next: (data) => {
        // Sort by weight descending (heaviest first)
        this.pokemons = data.sort((a, b) => b.weight - a.weight);
        this.applyFilter();
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading pokemons:', error);
        this.error = 'Failed to load Pokemon data';
        this.loading = false;
      }
    });
  }

  applyFilter(): void {
    switch (this.selectedWeightFilter) {
      case 'LIGHT':
        this.filteredPokemons = this.pokemons.filter(p => p.weight >= 100 && p.weight <= 150);
        break;
      case 'MEDIUM':
        this.filteredPokemons = this.pokemons.filter(p => p.weight >= 151 && p.weight <= 199);
        break;
      case 'HEAVY':
        this.filteredPokemons = this.pokemons.filter(p => p.weight >= 200);
        break;
      default:
        this.filteredPokemons = [...this.pokemons];
    }
  }

  onFilterChange(): void {
    this.applyFilter();
  }

  getImageUrl(imagePath: string): string {
  if (!imagePath) {
    return 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/1.png';
  }
  
  const filename = imagePath.split('/').pop();
  
  return `http://localhost:8000/storage/pokemon_images/${filename}`;
}

onImageError(event: Event): void {
  const imgElement = event.target as HTMLImageElement;
  imgElement.src = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/1.png';
}

  capitalizeFirstLetter(text: string): string {
    return text.charAt(0).toUpperCase() + text.slice(1);
  }
}