<?php

use App\Models\FavoritePokemon;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Http::preventStrayRequests();
});

test('guests are redirected away from the favorites index', function () {
    $this->get(route('favorites.index'))
        ->assertRedirect(route('login'));
});

test('guest cannot favorite pokemon', function () {
    $this->post(route('favorites.store'), [
        'pokemon_name' => 'squirtle',
    ])->assertRedirect(route('login'));

    $this->assertDatabaseCount('favorite_pokemons', 0);
});

test('guest cannot unfavorite pokemon', function () {
    $this->delete(route('favorites.destroy', 'squirtle'))
        ->assertRedirect(route('login'));
});

test('favoriting requires a pokemon name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('pokemon.index'))
        ->post(route('favorites.store'), [])
        ->assertSessionHasErrors('pokemon_name');
});

test('favoriting reports pokeapi failures', function () {
    Http::fake([
        'pokeapi.co/api/v2/pokemon/pikachu' => Http::response('Server error', 500),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('pokemon.show', 'pikachu'))
        ->post(route('favorites.store'), ['pokemon_name' => 'pikachu'])
        ->assertRedirect(route('pokemon.show', 'pikachu'))
        ->assertSessionHasErrors('pokemon_name');

    $this->assertDatabaseCount('favorite_pokemons', 0);
});

test('authenticated user can favorite pokemon', function () {
    fakePokemonDetailResponse('squirtle', 7, ['types' => ['water'], 'abilities' => ['torrent']]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('favorites.store'), [
            'pokemon_name' => 'squirtle',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('favorite_pokemons', [
        'user_id' => $user->id,
        'pokemon_name' => 'squirtle',
        'pokemon_api_id' => 7,
    ]);
});

test('favoriting the same pokemon twice keeps a single row', function () {
    fakePokemonDetailResponse('squirtle', 7, ['types' => ['water'], 'abilities' => ['torrent']]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('favorites.store'), ['pokemon_name' => 'squirtle'])
        ->assertRedirect();

    $this->actingAs($user)
        ->post(route('favorites.store'), ['pokemon_name' => 'squirtle'])
        ->assertRedirect();

    expect(
        FavoritePokemon::query()
            ->where('user_id', $user->id)
            ->where('pokemon_api_id', 7)
            ->count()
    )->toBe(1);
});

test('authenticated user can unfavorite their own pokemon', function () {
    $user = User::factory()->create();

    FavoritePokemon::factory()->for($user)->create([
        'pokemon_name' => 'squirtle',
        'pokemon_api_id' => 7,
    ]);

    $this->actingAs($user)
        ->delete(route('favorites.destroy', 'squirtle'))
        ->assertRedirect();

    $this->assertDatabaseMissing('favorite_pokemons', [
        'user_id' => $user->id,
        'pokemon_name' => 'squirtle',
    ]);
});

test("authenticated user cannot unfavorite another user's pokemon", function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    FavoritePokemon::factory()->for($alice)->create([
        'pokemon_name' => 'pikachu',
        'pokemon_api_id' => 25,
    ]);

    $this->actingAs($bob)
        ->delete(route('favorites.destroy', 'pikachu'))
        ->assertNotFound();

    $this->assertDatabaseHas('favorite_pokemons', [
        'user_id' => $alice->id,
        'pokemon_name' => 'pikachu',
        'pokemon_api_id' => 25,
    ]);
});

test('favorites are scoped per user', function () {
    fakePokemonDetailResponse('pikachu', 25);

    $alice = User::factory()->create();
    $bob = User::factory()->create();

    FavoritePokemon::factory()->for($bob)->create([
        'pokemon_name' => 'bulbasaur',
        'pokemon_api_id' => 1,
    ]);

    $this->actingAs($alice)
        ->post(route('favorites.store'), [
            'pokemon_name' => 'pikachu',
        ])
        ->assertRedirect();

    $this->actingAs($alice)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pokemon/Favorites')
            ->has('favorites', 1)
            ->where('favorites.0.name', 'pikachu')
        );

    $this->actingAs($bob)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pokemon/Favorites')
            ->has('favorites', 1)
            ->where('favorites.0.name', 'bulbasaur')
        );

    FavoritePokemon::factory()->for($alice)->create([
        'pokemon_name' => 'bulbasaur',
        'pokemon_api_id' => 1,
    ]);

    expect(FavoritePokemon::query()->where('pokemon_api_id', 1)->count())->toBe(2)
        ->and(FavoritePokemon::query()->where('user_id', $alice->id)->count())->toBe(2)
        ->and(FavoritePokemon::query()->where('user_id', $bob->id)->count())->toBe(1);
});

test('authenticated users can view their favorites index', function () {
    $user = User::factory()->create();

    FavoritePokemon::factory()->for($user)->create([
        'pokemon_name' => 'eevee',
        'pokemon_api_id' => 133,
    ]);

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pokemon/Favorites')
            ->has('favorites', 1)
            ->where('favorites.0.name', 'eevee')
            ->where('favorites.0.is_favorite', true)
        );
});
