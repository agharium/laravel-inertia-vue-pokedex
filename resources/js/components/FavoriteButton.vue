<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Heart } from '@lucide/vue';
import { computed, watch } from 'vue';
import {
    destroy as destroyFavorite,
    store as storeFavorite,
} from '@/actions/App/Http/Controllers/FavoritePokemonController';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';

const props = defineProps<{
    name: string;
    isFavorite: boolean;
}>();

const page = usePage();
const isAuthenticated = computed(() => page.props.auth.user !== null);

const form = useForm({
    pokemon_name: props.name,
});

watch(
    () => props.name,
    (name) => {
        form.pokemon_name = name;
    },
);

const label = computed(() => (props.isFavorite ? 'Unfavorite' : 'Favorite'));

function toggle(): void {
    if (form.processing) {
        return;
    }

    if (props.isFavorite) {
        form.delete(destroyFavorite.url(props.name), {
            preserveScroll: true,
        });

        return;
    }

    form.post(storeFavorite.url(), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Button
        v-if="isAuthenticated"
        type="button"
        :variant="isFavorite ? 'default' : 'outline'"
        size="sm"
        class="gap-2"
        :class="
            isFavorite
                ? 'bg-[#e3350d] text-white hover:bg-[#c42d0b]'
                : 'border-white/20 bg-white/5 text-slate-100 hover:bg-white/10'
        "
        :disabled="form.processing"
        @click="toggle"
    >
        <Heart class="size-4" :class="isFavorite ? 'fill-current' : ''" />
        {{ form.processing ? 'Saving…' : label }}
    </Button>
    <Link
        v-else
        :href="login()"
        class="text-xs font-medium text-slate-300 underline-offset-4 transition hover:text-white hover:underline"
    >
        Sign in to save favorites.
    </Link>
</template>
