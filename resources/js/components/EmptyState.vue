<script setup lang="ts">
defineProps<{
    title: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="rounded-2xl border border-dashed border-white/15 bg-slate-900/40 px-6 py-16 text-center"
        role="status"
    >
        <p class="text-lg font-medium text-white">{{ title }}</p>
        <p v-if="description" class="mt-2 text-sm text-slate-400">
            {{ description }}
        </p>
        <div v-if="$slots.actions" class="mt-6 flex justify-center">
            <slot name="actions" />
        </div>
    </div>
</template>
