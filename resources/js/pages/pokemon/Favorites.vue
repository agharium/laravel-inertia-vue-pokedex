<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PokemonCard from '@/components/PokemonCard.vue';
import { index as pokemonIndex } from '@/routes/pokemon';
import type { PokemonSummary } from '@/types/pokemon';

defineProps<{
    favorites: PokemonSummary[];
}>();
</script>

<template>
    <div>
        <Head title="Favorites" />

        <section class="mb-8 space-y-2">
            <h1
                class="text-3xl font-semibold tracking-tight text-white sm:text-4xl"
            >
                Your favorites
            </h1>
            <p class="max-w-2xl text-slate-300">
                Pokémon you have saved to your account.
            </p>
        </section>

        <EmptyState
            v-if="favorites.length === 0"
            title="No favorites yet"
            description="Browse the Pokédex and tap Favorite on any Pokémon."
        >
            <template #actions>
                <Link
                    :href="pokemonIndex()"
                    class="inline-flex rounded-xl bg-[#e3350d] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#c42d0b]"
                >
                    Browse Pokémon
                </Link>
            </template>
        </EmptyState>

        <div
            v-else
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
        >
            <PokemonCard
                v-for="item in favorites"
                :key="item.id"
                :pokemon="item"
            />
        </div>
    </div>
</template>
