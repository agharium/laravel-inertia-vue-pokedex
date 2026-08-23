<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

function fakePokemonListResponse(): void
{
    Http::fake([
        'pokeapi.co/api/v2/pokemon*' => Http::response([
            'count' => 2,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'name' => 'bulbasaur',
                    'url' => 'https://pokeapi.co/api/v2/pokemon/1/',
                ],
                [
                    'name' => 'ivysaur',
                    'url' => 'https://pokeapi.co/api/v2/pokemon/2/',
                ],
            ],
        ]),
    ]);
}

/**
 * @param  array{types?: list<string>, abilities?: list<string>}  $overrides
 */
function fakePokemonDetailResponse(string $name = 'pikachu', int $id = 25, array $overrides = []): void
{
    $types = $overrides['types'] ?? ['electric'];
    $abilities = $overrides['abilities'] ?? ['static'];

    Http::fake([
        "pokeapi.co/api/v2/pokemon/{$name}" => Http::response([
            'id' => $id,
            'name' => $name,
            'height' => 4,
            'weight' => 60,
            'sprites' => [
                'front_default' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
            ],
            'types' => array_map(
                fn (string $type): array => ['type' => ['name' => $type]],
                $types,
            ),
            'abilities' => array_map(
                fn (string $ability): array => ['ability' => ['name' => $ability]],
                $abilities,
            ),
            'stats' => [
                ['base_stat' => 35, 'stat' => ['name' => 'hp']],
                ['base_stat' => 55, 'stat' => ['name' => 'attack']],
            ],
        ]),
        'pokeapi.co/api/v2/pokemon*' => Http::response([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'name' => $name,
                    'url' => "https://pokeapi.co/api/v2/pokemon/{$id}/",
                ],
            ],
        ]),
    ]);
}
