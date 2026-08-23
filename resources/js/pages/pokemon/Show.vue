<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import FavoriteButton from '@/components/FavoriteButton.vue';
import PokemonSprite from '@/components/PokemonSprite.vue';
import StatBar from '@/components/StatBar.vue';
import { formatPokemonName, typeBadgeClass } from '@/lib/pokemon';
import { index as pokemonIndex } from '@/routes/pokemon';
import type { PokemonDetail } from '@/types/pokemon';

defineProps<{
    pokemon: PokemonDetail;
}>();
</script>

<template>
    <div>
        <Head :title="formatPokemonName(pokemon.name)" />

        <Link
            :href="pokemonIndex()"
            class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-slate-300 transition hover:text-white"
        >
            <ArrowLeft class="size-4" />
            Back to list
        </Link>

        <div
            class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70"
        >
            <div
                class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[280px_1fr] lg:gap-10"
            >
                <div class="text-center lg:text-left">
                    <div
                        class="mx-auto flex size-56 items-center justify-center rounded-full bg-slate-950/50 ring-1 ring-white/10 lg:mx-0"
                    >
                        <PokemonSprite
                            :name="pokemon.name"
                            :sprite-url="pokemon.sprite_url"
                            size-class="size-48"
                        />
                    </div>

                    <p class="mt-4 text-sm tracking-wide text-slate-400">
                        #{{ String(pokemon.id).padStart(3, '0') }}
                    </p>
                    <h1
                        class="mt-1 text-3xl font-semibold tracking-tight text-white sm:text-4xl"
                    >
                        {{ formatPokemonName(pokemon.name) }}
                    </h1>

                    <div
                        class="mt-4 flex flex-wrap justify-center gap-2 lg:justify-start"
                    >
                        <span
                            v-for="type in pokemon.types"
                            :key="type"
                            class="rounded-full px-3 py-1 text-xs font-semibold tracking-wide uppercase"
                            :class="typeBadgeClass(type)"
                        >
                            {{ type }}
                        </span>
                    </div>

                    <div class="mt-6 flex justify-center lg:justify-start">
                        <FavoriteButton
                            :name="pokemon.name"
                            :is-favorite="pokemon.is_favorite"
                        />
                    </div>
                </div>

                <div class="space-y-8">
                    <section>
                        <h2
                            class="mb-3 text-sm font-semibold tracking-[0.18em] text-slate-400 uppercase"
                        >
                            Profile
                        </h2>
                        <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            <div
                                class="rounded-xl border border-white/10 bg-slate-950/40 p-4"
                            >
                                <dt class="text-xs text-slate-400">Height</dt>
                                <dd
                                    class="mt-1 text-lg font-semibold text-white"
                                >
                                    {{ (pokemon.height / 10).toFixed(1) }} m
                                </dd>
                            </div>
                            <div
                                class="rounded-xl border border-white/10 bg-slate-950/40 p-4"
                            >
                                <dt class="text-xs text-slate-400">Weight</dt>
                                <dd
                                    class="mt-1 text-lg font-semibold text-white"
                                >
                                    {{ (pokemon.weight / 10).toFixed(1) }} kg
                                </dd>
                            </div>
                            <div
                                class="col-span-2 rounded-xl border border-white/10 bg-slate-950/40 p-4 sm:col-span-1"
                            >
                                <dt class="text-xs text-slate-400">
                                    Abilities
                                </dt>
                                <dd
                                    class="mt-1 text-sm font-medium text-white capitalize"
                                >
                                    {{
                                        pokemon.abilities
                                            .map((ability) =>
                                                ability.replaceAll('-', ' '),
                                            )
                                            .join(', ')
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section>
                        <h2
                            class="mb-4 text-sm font-semibold tracking-[0.18em] text-slate-400 uppercase"
                        >
                            Base stats
                        </h2>
                        <div class="space-y-3">
                            <StatBar
                                v-for="stat in pokemon.stats"
                                :key="stat.name"
                                :name="stat.name"
                                :value="stat.base_stat"
                            />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
