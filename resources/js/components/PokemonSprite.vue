<script setup lang="ts">
import { ref, watch } from 'vue';
import { formatPokemonName } from '@/lib/pokemon';

const props = withDefaults(
    defineProps<{
        name: string;
        spriteUrl: string | null;
        sizeClass?: string;
    }>(),
    {
        sizeClass: 'size-24',
    },
);

const imageFailed = ref(false);

watch(
    () => props.spriteUrl,
    () => {
        imageFailed.value = false;
    },
);
</script>

<template>
    <div class="flex items-center justify-center">
        <img
            v-if="spriteUrl && !imageFailed"
            :src="spriteUrl"
            :alt="formatPokemonName(name)"
            :class="['object-contain drop-shadow-lg', sizeClass]"
            loading="lazy"
            @error="imageFailed = true"
        />
        <span v-else class="text-sm font-medium text-slate-500">No image</span>
    </div>
</template>
