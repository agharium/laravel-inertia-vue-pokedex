<?php

namespace App\Services;

use App\Clients\PokeApiClient;
use App\Exceptions\PokeApiException;
use App\Models\FavoritePokemon;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PokemonService
{
    public function __construct(private readonly PokeApiClient $pokeApiClient) {}

    public function listPokemon(int $page = 1, int $perPage = 20, ?string $search = null, ?User $user = null): array
    {
        $page = max(1, $page);
        $perPage = min(max($perPage, 1), 50);
        $search = $search !== null ? Str::lower(trim($search)) : null;

        if ($search !== null && $search !== '') {
            return $this->searchByName($search, $page, $perPage, $user);
        }

        $offset = ($page - 1) * $perPage;
        $payload = $this->pokeApiClient->listPokemon($perPage, $offset);
        $favoriteNames = $this->favoriteNames($user);

        $data = collect($payload['results'])
            ->map(fn (array $result): array => $this->summaryFromListResult($result, $favoriteNames))
            ->all();

        return $this->paginatedList($data, $page, $perPage, (int) $payload['count'], $offset);
    }

    public function getPokemon(string $name, ?User $user = null): array
    {
        return $this->mapDetail($this->pokeApiClient->getPokemon($name), $user);
    }

    public function favorite(User $user, string $name): FavoritePokemon
    {
        $pokemon = $this->getPokemon($name);

        return $user->favoritePokemons()->updateOrCreate(
            ['pokemon_api_id' => $pokemon['id']],
            [
                'pokemon_name' => $pokemon['name'],
                'sprite_url' => $pokemon['sprite_url'],
            ],
        );
    }

    public function unfavorite(User $user, string $name): bool
    {
        return $user->favoritePokemons()
            ->where('pokemon_name', Str::lower($name))
            ->delete() > 0;
    }

    private function isFavorite(?User $user, string $name): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->favoritePokemons()
            ->where('pokemon_name', Str::lower($name))
            ->exists();
    }

    public function listFavorites(User $user): array
    {
        return $user->favoritePokemons()
            ->latest()
            ->get()
            ->map(fn (FavoritePokemon $favorite): array => [
                'id' => $favorite->pokemon_api_id,
                'name' => $favorite->pokemon_name,
                'sprite_url' => $favorite->sprite_url,
                'is_favorite' => true,
            ])
            ->all();
    }

    public function emptyList(int $page = 1, int $perPage = 20): array
    {
        return $this->emptyPage($page, $perPage);
    }

    private function searchByName(string $search, int $page, int $perPage, ?User $user): array
    {
        try {
            $pokemon = $this->getPokemon($search, $user);
        } catch (PokeApiException $exception) {
            if ($exception->isNotFound()) {
                return $this->emptyPage($page, $perPage);
            }

            throw $exception;
        }

        return [
            'data' => [$pokemon],
            'meta' => [
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 1,
            ],
            'links' => ['prev' => null, 'next' => null],
        ];
    }

    private function paginatedList(array $data, int $page, int $perPage, int $total, int $offset): array
    {
        $lastPage = max(1, (int) ceil($total / $perPage));
        $count = count($data);

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $count > 0 ? $offset + 1 : null,
                'to' => $count > 0 ? $offset + $count : null,
            ],
            'links' => [
                'prev' => $page > 1 ? route('pokemon.index', ['page' => $page - 1]) : null,
                'next' => $page < $lastPage ? route('pokemon.index', ['page' => $page + 1]) : null,
            ],
        ];
    }

    private function summaryFromListResult(array $result, Collection $favoriteNames): array
    {
        $id = (int) basename(parse_url($result['url'], PHP_URL_PATH) ?: $result['url']);
        $name = $result['name'];

        return [
            'id' => $id,
            'name' => $name,
            'sprite_url' => $this->spriteUrl($id),
            'is_favorite' => $favoriteNames->contains($name),
        ];
    }

    private function mapDetail(array $payload, ?User $user): array
    {
        $id = (int) $payload['id'];
        $name = (string) $payload['name'];
        $sprites = is_array($payload['sprites'] ?? null) ? $payload['sprites'] : [];
        $spriteUrl = $sprites['front_default'] ?? $this->spriteUrl($id);

        $types = is_array($payload['types'] ?? null) ? $payload['types'] : [];
        $abilities = is_array($payload['abilities'] ?? null) ? $payload['abilities'] : [];
        $stats = is_array($payload['stats'] ?? null) ? $payload['stats'] : [];

        return [
            'id' => $id,
            'name' => $name,
            'sprite_url' => is_string($spriteUrl) ? $spriteUrl : $this->spriteUrl($id),
            'types' => array_values(array_map(fn (array $type): string => $type['type']['name'], $types)),
            'height' => (int) ($payload['height'] ?? 0),
            'weight' => (int) ($payload['weight'] ?? 0),
            'abilities' => array_values(array_map(fn (array $ability): string => $ability['ability']['name'], $abilities)),
            'stats' => array_values(array_map(
                fn (array $stat): array => [
                    'name' => $stat['stat']['name'],
                    'base_stat' => (int) $stat['base_stat'],
                ],
                $stats,
            )),
            'is_favorite' => $this->isFavorite($user, $name),
        ];
    }

    private function emptyPage(int $page, int $perPage): array
    {
        return [
            'data' => [],
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => 0,
                'last_page' => 1,
                'from' => null,
                'to' => null,
            ],
            'links' => ['prev' => null, 'next' => null],
        ];
    }

    private function favoriteNames(?User $user): Collection
    {
        if ($user === null) {
            return collect();
        }

        return $user->favoritePokemons()->pluck('pokemon_name');
    }

    private function spriteUrl(int $id): string
    {
        return "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png";
    }
}
