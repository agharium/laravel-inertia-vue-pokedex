<script setup lang="ts">
import { computed } from 'vue';
import { formatStatName } from '@/lib/pokemon';

const props = withDefaults(
    defineProps<{
        name: string;
        value: number;
        max?: number;
    }>(),
    {
        max: 255,
    },
);

const width = computed(() =>
    Math.min(100, Math.round((props.value / props.max) * 100)),
);
</script>

<template>
    <div class="grid grid-cols-[6.5rem_1fr_2.5rem] items-center gap-3">
        <span class="text-sm font-medium text-slate-300">
            {{ formatStatName(name) }}
        </span>
        <div class="h-2.5 overflow-hidden rounded-full bg-slate-800">
            <div
                class="h-full rounded-full bg-gradient-to-r from-[#e3350d] to-amber-400 transition-all duration-500"
                :style="{ width: `${width}%` }"
            />
        </div>
        <span class="text-right text-sm text-slate-200 tabular-nums">
            {{ value }}
        </span>
    </div>
</template>
