<?php

namespace App\Clients;

use App\Exceptions\PokeApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PokeApiClient
{
    public function listPokemon(int $limit = 20, int $offset = 0): array
    {
        return $this->remember("pokeapi.list.{$limit}.{$offset}", fn (): array => $this->http()->get('/pokemon', [
            'limit' => $limit,
            'offset' => $offset,
        ])->json());
    }

    public function getPokemon(string $nameOrId): array
    {
        $key = Str::lower($nameOrId);

        return $this->remember("pokeapi.pokemon.{$key}", fn (): array => $this->http()->get("/pokemon/{$key}")->json());
    }

    private function remember(string $cacheKey, callable $callback): mixed
    {
        try {
            return Cache::remember($cacheKey, (int) config('services.pokeapi.cache_ttl', 3600), $callback);
        } catch (ConnectionException $exception) {
            throw PokeApiException::connectionFailed($exception);
        } catch (RequestException $exception) {
            throw PokeApiException::fromResponse($exception->response);
        }
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl((string) config('services.pokeapi.base_url'))
            ->acceptJson()
            ->timeout((int) config('services.pokeapi.timeout', 5))
            ->connectTimeout(3)
            ->retry(2, 200, fn ($exception) => $exception instanceof ConnectionException)
            ->throw();
    }
}
