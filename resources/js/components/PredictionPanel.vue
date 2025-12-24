<template>
    <div
        class="bg-white dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm h-full flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Championship Odds</h3>
            <span class="material-symbols-outlined text-slate-400 text-lg">trending_up</span>
        </div>

        <div v-if="predictions.length === 0" class="flex-1 flex items-center justify-center text-slate-400 text-sm">
            Predictions will be visible after 4 weeks
        </div>

        <div v-else class="flex flex-col gap-4 flex-1">
            <div v-for="(team, index) in predictions" :key="team.team_id" class="flex flex-col gap-2"
                :class="{ 'opacity-60': index > 2 }">
                <div class="flex justify-between items-end">
                    <div class="flex items-center gap-2">
                        <div
                            class="size-5 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[8px] font-bold">
                            {{ team.short_name }}
                        </div>
                        <span class="text-sm font-semibold dark:text-white">{{ team.team_name }}</span>
                    </div>
                    <span :class="[
                        'text-sm font-bold',
                        index === 0 ? 'text-primary' : 'text-slate-600 dark:text-slate-300'
                    ]">
                        {{ team.probability }}%
                    </span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5">
                    <div :class="[
                        'h-2.5 rounded-full transition-all duration-500',
                        index === 0 ? 'bg-primary' : 'bg-slate-400 dark:bg-slate-500'
                    ]" :style="{ width: team.probability + '%' }"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    predictions: {
        type: Array,
        default: () => []
    }
});
</script>