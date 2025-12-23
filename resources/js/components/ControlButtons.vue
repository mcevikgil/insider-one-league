<template>
    <div
        class="sticky bottom-0 z-30 w-full bg-white dark:bg-[#111318] border-t border-slate-200 dark:border-slate-800 p-4 lg:px-8 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <div class="max-w-[1600px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="hidden sm:flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Simulation Control
                </span>
                <span class="text-sm font-medium dark:text-white">
                    {{ statusText }}
                </span>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button v-if="!showPlayButtons" @click="$emit('startSimulate')" :disabled="loading" :class="[
                    'flex-1 sm:flex-none h-11 px-6 rounded-lg font-bold text-sm transition-colors border',
                    loading
                        ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-200 dark:border-slate-700 cursor-not-allowed'
                        : 'bg-slate-100 dark:bg-surface-dark-lighter hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white border-slate-200 dark:border-slate-700'
                ]">
                    Start Simulate
                </button>
                <button v-if="showPlayButtons" @click="$emit('simulateAll')" :disabled="allPlayed || loading" :class="[
                    'flex-1 sm:flex-none h-11 px-6 rounded-lg font-bold text-sm transition-colors border',
                    allPlayed || loading
                        ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-200 dark:border-slate-700 cursor-not-allowed'
                        : 'bg-slate-100 dark:bg-surface-dark-lighter hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white border-slate-200 dark:border-slate-700'
                ]">
                    Play All Weeks
                </button>
                <button v-if="showPlayButtons" @click="$emit('simulateWeek')" :disabled="allPlayed || loading" :class="[
                    'flex-1 sm:flex-none h-11 px-8 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2',
                    allPlayed || loading
                        ? 'bg-slate-300 dark:bg-slate-700 text-slate-500 cursor-not-allowed'
                        : 'bg-primary hover:bg-blue-600 text-white shadow-lg shadow-blue-500/20'
                ]">
                    <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                    Play Week {{ currentWeek }}
                </button>
                <button @click="$emit('reset')" :disabled="loading"
                    class="size-11 flex items-center justify-center rounded-lg border border-red-200 dark:border-red-900/30 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Reset League">
                    <span class="material-symbols-outlined">restart_alt</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    showPlayButtons: {
        type: Boolean,
        default: false
    },
    currentWeek: {
        type: Number,
        default: 1
    },
    allPlayed: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['simulateWeek', 'simulateAll', 'reset', 'startSimulate']);

const statusText = computed(() => {
    if (props.loading) return 'Simulating...';
    if (props.allPlayed) return 'Season completed!';
    return `Ready to simulate Week ${props.currentWeek}`;
});
</script>