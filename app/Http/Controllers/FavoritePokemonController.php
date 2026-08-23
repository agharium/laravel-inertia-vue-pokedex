<?php

namespace App\Http\Controllers;

use App\Exceptions\PokeApiException;
use App\Http\Requests\StoreFavoritePokemonRequest;
use App\Services\PokemonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoritePokemonController extends Controller
{
    public function __construct(private readonly PokemonService $pokemonService) {}

    public function index(Request $request): Response
    {
        return Inertia::render('pokemon/Favorites', [
            'favorites' => $this->pokemonService->listFavorites($request->user()),
        ]);
    }

    public function store(StoreFavoritePokemonRequest $request): RedirectResponse
    {
        $name = $request->validated('pokemon_name');

        try {
            $this->pokemonService->favorite($request->user(), $name);
        } catch (PokeApiException $exception) {
            report($exception);

            return back()->withErrors([
                'pokemon_name' => $exception->getMessage(),
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Added to favorites.'),
        ]);

        return back();
    }

    public function destroy(Request $request, string $pokemon): RedirectResponse
    {
        $removed = $this->pokemonService->unfavorite($request->user(), $pokemon);

        if (! $removed) {
            abort(404);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Removed from favorites.'),
        ]);

        return back();
    }
}
