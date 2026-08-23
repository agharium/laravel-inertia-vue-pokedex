# Pokédex Favorites

A Laravel + Inertia + Vue app that lists Pokémon from [PokéAPI](https://pokeapi.co/) and lets signed-in users save favorites to their account.

Guests can browse and search. After registration, favorites are stored per user and protected against duplicates.

## Stack

- PHP 8.3+ / Laravel 13
- Inertia.js 3 / Vue 3
- Tailwind CSS 4
- SQLite
- Laravel Fortify
- Pest

## What it does

- Paginated list with exact-name search
- Detail pages (types, abilities, height/weight, base stats)
- Cached PokéAPI HTTP calls, with timeouts
- Registration, login, and account settings
- User-scoped favorites

## Setup

You need PHP 8.3+, Composer, and Node.js 22+.

One command:

```bash
composer run setup
```

Or step by step:

```bash
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate

npm install
npm run build
```

Then:

```bash
composer run dev
```

Open `http://localhost:8000`.

PokéAPI settings in `.env`:

```env
POKEAPI_BASE_URL=https://pokeapi.co/api/v2
POKEAPI_TIMEOUT=5
POKEAPI_CACHE_TTL=3600
```

## Tests

```bash
php artisan test --compact
```

Full lint + types + tests (what CI runs):

```bash
composer ci:check
```

HTTP calls to PokéAPI are faked in tests.

## Layout

- `PokemonController` / `FavoritePokemonController` handle HTTP and Inertia pages
- `PokemonService` maps API data and favorite state
- `PokeApiClient` is the HTTP/cache boundary for PokéAPI
- `FavoritePokemon` is a normal Eloquent model, scoped by `user_id`

Vue pages own the screens; cards and the favorite button are shared components.

## Notes

Props are plain arrays — no DTOs or API resources. SQLite is enough for local setup. A unique index on `user_id` + `pokemon_api_id` is what prevents duplicate favorites. Auth screens come from Laravel's Vue starter kit rather than being rewritten from scratch.
