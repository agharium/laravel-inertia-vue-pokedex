<?php

namespace App\Models;

use Database\Factories\FavoritePokemonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $pokemon_name
 * @property int $pokemon_api_id
 * @property string|null $sprite_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
#[Fillable(['pokemon_name', 'pokemon_api_id', 'sprite_url'])]
class FavoritePokemon extends Model
{
    /** @use HasFactory<FavoritePokemonFactory> */
    use HasFactory;

    protected $table = 'favorite_pokemons';

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'pokemon_api_id' => 'integer',
        ];
    }
}
