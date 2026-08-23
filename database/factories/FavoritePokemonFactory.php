<?php

namespace Database\Factories;

use App\Models\FavoritePokemon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FavoritePokemon>
 */
class FavoritePokemonFactory extends Factory
{
    protected $model = FavoritePokemon::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = fake()->unique()->numberBetween(1, 151);

        return [
            'user_id' => User::factory(),
            'pokemon_name' => fake()->unique()->slug(1),
            'pokemon_api_id' => $id,
            'sprite_url' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
        ];
    }
}
