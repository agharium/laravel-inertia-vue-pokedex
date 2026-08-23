<?php

namespace App\Http\Controllers;

use App\Exceptions\PokeApiException;
use App\Services\PokemonService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PokemonController extends Controller
{
    public function __construct(private readonly PokemonService $pokemonService) {}

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $page = max(1, $request->integer('page', 1));
        $user = $request->user();

        try {
            $pokemon = $this->pokemonService->listPokemon(
                page: $page,
                search: $search !== '' ? $search : null,
                user: $user,
            );
            $error = null;
        } catch (PokeApiException $exception) {
            report($exception);

            $pokemon = $this->pokemonService->emptyList($page);
            $error = $exception->getMessage();
        }

        return Inertia::render('pokemon/Index', [
            'pokemon' => $pokemon,
            'filters' => [
                'search' => $search,
            ],
            'error' => $error,
        ]);
    }

    public function show(Request $request, string $pokemon): Response
    {
        try {
            $details = $this->pokemonService->getPokemon($pokemon, $request->user());
        } catch (PokeApiException $exception) {
            report($exception);

            if ($exception->isNotFound()) {
                abort(404, $exception->getMessage());
            }

            abort(503, $exception->getMessage());
        }

        return Inertia::render('pokemon/Show', [
            'pokemon' => $details,
        ]);
    }
}
