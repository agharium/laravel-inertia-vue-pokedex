<?php

use App\Http\Controllers\FavoritePokemonController;
use App\Http\Controllers\PokemonController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/pokemon')->name('home');

Route::get('/pokemon', [PokemonController::class, 'index'])->name('pokemon.index');
Route::get('/pokemon/{pokemon}', [PokemonController::class, 'show'])
    ->where('pokemon', '[A-Za-z0-9\-]+')
    ->name('pokemon.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/favorites', [FavoritePokemonController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [FavoritePokemonController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{pokemon}', [FavoritePokemonController::class, 'destroy'])
        ->where('pokemon', '[A-Za-z0-9\-]+')
        ->name('favorites.destroy');
});

require __DIR__.'/settings.php';
