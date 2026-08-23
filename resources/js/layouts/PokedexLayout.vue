<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Heart } from '@lucide/vue';
import { computed } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { login, logout, register } from '@/routes';
import { index as favoritesIndex } from '@/routes/favorites';
import { index as pokemonIndex } from '@/routes/pokemon';
import { edit as editProfile } from '@/routes/profile';

const page = usePage();
const user = computed(() => page.props.auth.user);
const favoritesCount = computed(() => Number(page.props.favoritesCount ?? 0));
const { isCurrentUrl } = useCurrentUrl();

function linkClass(href: string): string {
    return [
        'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
        isCurrentUrl(href)
            ? 'bg-white/15 text-white'
            : 'text-slate-200 hover:bg-white/10 hover:text-white',
    ].join(' ');
}
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="border-b border-white/10">
            <div
                class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6"
            >
                <Link
                    :href="pokemonIndex()"
                    class="group flex items-center gap-3"
                >
                    <span
                        class="flex size-10 items-center justify-center rounded-full bg-[#e3350d] text-sm font-bold text-white"
                    >
                        PK
                    </span>
                    <p class="text-lg font-semibold tracking-tight text-white">
                        Pokédex Favorites
                    </p>
                </Link>

                <nav
                    class="flex flex-wrap items-center justify-end gap-1 sm:gap-2"
                >
                    <Link
                        :href="pokemonIndex()"
                        :class="linkClass(pokemonIndex().url)"
                    >
                        Browse
                    </Link>
                    <Link
                        v-if="user"
                        :href="favoritesIndex()"
                        :class="linkClass(favoritesIndex().url)"
                        class="inline-flex items-center gap-2"
                    >
                        <Heart class="size-4" />
                        Favorites
                        <span
                            v-if="favoritesCount > 0"
                            class="rounded-full bg-[#e3350d] px-2 py-0.5 text-xs font-semibold text-white"
                        >
                            {{ favoritesCount }}
                        </span>
                    </Link>

                    <template v-if="user">
                        <span
                            class="hidden px-2 text-sm text-slate-400 sm:inline"
                        >
                            {{ user.name }}
                        </span>
                        <Link
                            :href="editProfile()"
                            :class="linkClass(editProfile().url)"
                        >
                            Settings
                        </Link>
                        <Link
                            :href="logout()"
                            method="post"
                            as="button"
                            class="rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition-colors hover:bg-white/10 hover:text-white"
                        >
                            Log out
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="login()" :class="linkClass(login().url)">
                            Sign in
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-lg bg-[#e3350d] px-3 py-2 text-sm font-semibold text-white transition hover:bg-[#c42d0b]"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
            <slot />
        </main>

        <Toaster />
    </div>
</template>
