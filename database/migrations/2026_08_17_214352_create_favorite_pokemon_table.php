<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_pokemons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('pokemon_api_id');
            $table->string('pokemon_name');
            $table->string('sprite_url')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'pokemon_api_id']);
            $table->index(['user_id', 'pokemon_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_pokemons');
    }
};
