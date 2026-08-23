<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import PokemonCard from '@/components/PokemonCard.vue';
import { index as pokemonIndex } from '@/routes/pokemon';
import type { PokemonListPayload } from '@/types/pokemon';

const props = defineProps<{
    pokemon: PokemonListPayload;
    filters: {
        search: string;
    };
    error: string | null;
}>();

const search = ref(props.filters.search ?? '');
const isLoading = ref(false);

watch(
    () => props.filters.search,
    (value) => {
        search.value = value ?? '';
    },
);

function submitSearch(): void {
    router.get(
        pokemonIndex.url({
            query: {
                search: search.value || undefined,
            },
        }),
        {},
        {
            preserveState: true,
            replace: true,
            onStart: () => {
                isLoading.value = true;
            },
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
}

function clearSearch(): void {
    search.value = '';
    submitSearch();
}
</script>

<template>
    <div>
        <Head title="Pokémon" />

        <section class="mb-8 space-y-2">
            <h1
                class="text-3xl font-semibold tracking-tight text-white sm:text-4xl"
            >
                Pokédex
            </h1>
            <p class="max-w-2xl text-slate-300">
                Browse Pokémon or search by exact name.
            </p>
        </section>

        <form
            class="mb-8 flex flex-col gap-3 sm:flex-row"
            @submit.prevent="submitSearch"
        >
            <label class="relative grow">
                <span class="sr-only">Search Pokémon</span>
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400"
                />
                <input
                    v-model="search"
                    type="search"
                    name="search"
                    placeholder="Search by name (e.g. pikachu)"
                    class="w-full rounded-xl border border-white/10 bg-slate-950/60 py-3 pr-4 pl-10 text-slate-100 placeholder:text-slate-500 focus:border-[#e3350d]/60 focus:ring-2 focus:ring-[#e3350d]/30 focus:outline-none"
                />
            </label>
            <div class="flex gap-2">
                <button
                    type="submit"
                    class="rounded-xl bg-[#e3350d] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#c42d0b] disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="isLoading"
                >
                    {{ isLoading ? 'Searching…' : 'Search' }}
                </button>
                <button
                    v-if="filters.search"
                    type="button"
                    class="rounded-xl border border-white/15 px-5 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/5 disabled:opacity-60"
                    :disabled="isLoading"
                    @click="clearSearch"
                >
                    Clear
                </button>
            </div>
        </form>

        <div
            v-if="error"
            class="mb-8 rounded-xl border border-red-400/30 bg-red-950/40 px-4 py-3 text-sm text-red-100"
            role="alert"
        >
            {{ error }}
        </div>

        <div
            v-else-if="isLoading"
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
            aria-busy="true"
            aria-label="Loading Pokémon"
        >
            <div
                v-for="n in 8"
                :key="n"
                class="h-64 animate-pulse rounded-2xl border border-white/10 bg-slate-900/50"
            />
        </div>

        <EmptyState
            v-else-if="pokemon.data.length === 0"
            title="No Pokémon found"
            description="Try another name, or clear the search to browse the full list."
        />

        <div
            v-else
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
        >
            <PokemonCard
                v-for="item in pokemon.data"
                :key="item.id"
                :pokemon="item"
            />
        </div>

        <div
            v-if="pokemon.meta.last_page > 1 && !filters.search && !error"
            class="mt-10 flex flex-wrap items-center justify-between gap-4 border-t border-white/10 pt-6"
        >
            <p class="text-sm text-slate-400">
                Showing
                <span class="font-medium text-slate-200">
                    {{ pokemon.meta.from }}–{{ pokemon.meta.to }}
                </span>
                of
                <span class="font-medium text-slate-200">
                    {{ pokemon.meta.total }}
                </span>
            </p>

            <div class="flex gap-2">
                <Link
                    v-if="pokemon.links.prev"
                    :href="pokemon.links.prev"
                    class="rounded-lg border border-white/15 px-4 py-2 text-sm font-medium text-slate-100 transition hover:bg-white/5"
                    preserve-scroll
                >
                    Previous
                </Link>
                <span
                    v-else
                    class="rounded-lg border border-white/5 px-4 py-2 text-sm text-slate-600"
                >
                    Previous
                </span>

                <Link
                    v-if="pokemon.links.next"
                    :href="pokemon.links.next"
                    class="rounded-lg border border-white/15 px-4 py-2 text-sm font-medium text-slate-100 transition hover:bg-white/5"
                    preserve-scroll
                >
                    Next
                </Link>
                <span
                    v-else
                    class="rounded-lg border border-white/5 px-4 py-2 text-sm text-slate-600"
                >
                    Next
                </span>
            </div>
        </div>
    </div>
</template>
