<?php

use App\Clients\PokeApiClient;
use App\Exceptions\PokeApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Http::preventStrayRequests();
    Cache::flush();
});

test('list pokemon caches the pokeapi response', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon*' => Http::response([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'name' => 'bulbasaur',
                    'url' => 'https://pokeapi.co/api/v2/pokemon/1/',
                ],
            ],
        ]),
    ]);

    $client = app(PokeApiClient::class);

    $client->listPokemon(20, 0);
    $client->listPokemon(20, 0);

    Http::assertSentCount(1);
});

test('list pokemon caches each page separately', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon*' => Http::response([
            'count' => 40,
            'results' => [],
        ]),
    ]);

    $client = app(PokeApiClient::class);

    $client->listPokemon(20, 0);
    $client->listPokemon(20, 20);
    $client->listPokemon(20, 0);
    $client->listPokemon(20, 20);

    Http::assertSentCount(2);
});

test('get pokemon caches individual details', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon/pikachu' => Http::response([
            'id' => 25,
            'name' => 'pikachu',
            'sprites' => ['front_default' => null],
            'types' => [],
            'abilities' => [],
            'stats' => [],
            'height' => 4,
            'weight' => 60,
        ]),
    ]);

    $client = app(PokeApiClient::class);

    $client->getPokemon('Pikachu');
    $client->getPokemon('pikachu');

    Http::assertSentCount(1);
});

test('client throws a domain exception when pokeapi returns not found', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon/missingno' => Http::response(['detail' => 'Not found'], 404),
    ]);

    app(PokeApiClient::class)->getPokemon('missingno');
})->throws(PokeApiException::class, 'Pokémon not found.');
