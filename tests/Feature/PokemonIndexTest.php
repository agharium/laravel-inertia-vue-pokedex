<?php

use App\Models\FavoritePokemon;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Http::preventStrayRequests();
});

test('pokemon index page renders list from pokeapi', function () {
    fakePokemonListResponse();

    $this->get(route('pokemon.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pokemon/Index')
            ->has('pokemon.data', 2)
            ->where('pokemon.data.0.name', 'bulbasaur')
            ->where('error', null)
        );
});

test('pokemon index search returns a matching pokemon', function () {
    fakePokemonDetailResponse('pikachu', 25);

    $this->get(route('pokemon.index', ['search' => 'pikachu']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pokemon/Index')
            ->has('pokemon.data', 1)
            ->where('pokemon.data.0.name', 'pikachu')
            ->where('filters.search', 'pikachu')
        );
});

test('pokemon index handles api failures gracefully', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon*' => Http::response('Server error', 500),
    ]);

    $this->get(route('pokemon.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pokemon/Index')
            ->has('pokemon.data', 0)
            ->where('error', 'PokéAPI is temporarily unavailable. Please try again later.')
        );
});

test('pokemon show page renders details', function () {
    fakePokemonDetailResponse('charmander', 4, ['types' => ['fire']]);

    $this->get(route('pokemon.show', 'charmander'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pokemon/Show')
            ->where('pokemon.name', 'charmander')
            ->where('pokemon.id', 4)
            ->where('pokemon.types.0', 'fire')
            ->has('pokemon.stats', 2)
            ->where('pokemon.is_favorite', false)
        );
});

test('pokemon show returns not found for unknown pokemon', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon/missingno' => Http::response(['detail' => 'Not found'], 404),
    ]);

    $this->get(route('pokemon.show', 'missingno'))
        ->assertNotFound();
});

test('pokemon show returns service unavailable when pokeapi fails', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon/pikachu' => Http::response('Server error', 500),
    ]);

    $this->get(route('pokemon.show', 'pikachu'))
        ->assertServiceUnavailable();
});

test("pokemon show identifies a user's favorite", function () {
    fakePokemonDetailResponse('pikachu', 25);

    $user = User::factory()->create();
    FavoritePokemon::factory()->for($user)->create([
        'pokemon_name' => 'pikachu',
        'pokemon_api_id' => 25,
    ]);

    $this->actingAs($user)
        ->get(route('pokemon.show', 'pikachu'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('pokemon.is_favorite', true)
        );
});
