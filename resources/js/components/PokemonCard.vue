<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import FavoriteButton from '@/components/FavoriteButton.vue';
import PokemonSprite from '@/components/PokemonSprite.vue';
import { formatPokemonName } from '@/lib/pokemon';
import { show as pokemonShow } from '@/routes/pokemon';
import type { PokemonSummary } from '@/types/pokemon';

defineProps<{
    pokemon: PokemonSummary;
}>();
</script>

<template>
    <article
        class="group rounded-2xl border border-white/10 bg-slate-900/60 p-4 transition hover:border-white/20"
    >
        <div class="flex items-start justify-between gap-2">
            <p class="text-xs font-medium tracking-wide text-slate-400">
                #{{ String(pokemon.id).padStart(3, '0') }}
            </p>
            <FavoriteButton
                :name="pokemon.name"
                :is-favorite="pokemon.is_favorite"
            />
        </div>

        <Link
            :href="pokemonShow(pokemon.name)"
            class="mt-2 block text-center"
            prefetch
        >
            <div
                class="mx-auto mb-3 flex size-28 items-center justify-center rounded-full bg-slate-950/50 ring-1 ring-white/10 transition group-hover:ring-[#e3350d]/40"
            >
                <PokemonSprite
                    :name="pokemon.name"
                    :sprite-url="pokemon.sprite_url"
                    class="transition duration-300 group-hover:scale-110"
                />
            </div>

            <h2
                class="text-lg font-semibold text-white transition group-hover:text-[#ffb4a4]"
            >
                {{ formatPokemonName(pokemon.name) }}
            </h2>
            <p class="mt-1 text-sm text-slate-400">View details →</p>
        </Link>
    </article>
</template>
