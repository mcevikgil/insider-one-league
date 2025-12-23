<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">calendar_month</span>
                <h3 class="text-xl font-bold dark:text-white">Tournament Fixtures</h3>
            </div>
        </div>

        <div v-if="matches.length === 0" class="text-center py-12 text-slate-400">
            <span class="material-symbols-outlined text-5xl mb-4 block">sports_soccer</span>
            <p>Select 4 teams and generate fixtures</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <div v-for="week in 6" :key="week"
                class="group bg-white dark:bg-surface-dark rounded-xl border border-gray-200 dark:border-border-dark overflow-hidden hover:border-primary/50 transition-colors">
                <div
                    class="bg-slate-50 dark:bg-[#15181e] px-5 py-3 border-b border-gray-200 dark:border-border-dark flex justify-between items-center">
                    <span class="text-primary font-bold">Week {{ week }}</span>
                    <span :class="[
                        'text-xs font-medium px-2 py-0.5 rounded',
                        isWeekPlayed(week) ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-400 dark:bg-white/5'
                    ]">
                        {{ isWeekPlayed(week) ? 'Played' : 'Pending' }}
                    </span>
                </div>
                <div class="p-4 space-y-4">
                    <div v-for="(match, index) in getWeekMatches(week)" :key="match.id"
                        :class="{ 'border-t border-dashed border-gray-200 dark:border-border-dark pt-3': index > 0 }">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 flex-1">
                                <div class="size-2 rounded-full bg-blue-500"></div>
                                <span class="text-sm font-medium truncate dark:text-white">
                                    {{ match.home_team?.name || 'TBD' }}
                                </span>
                            </div>
                            <div
                                class="px-3 text-sm font-bold text-slate-600 dark:text-white bg-slate-100 dark:bg-white/5 rounded py-1 min-w-[60px] text-center">
                                <template v-if="match.is_played">
                                    {{ match.home_score }} - {{ match.away_score }}
                                </template>
                                <template v-else>
                                    VS
                                </template>
                            </div>
                            <div class="flex items-center justify-end gap-2 flex-1">
                                <span class="text-sm font-medium truncate text-right dark:text-white">
                                    {{ match.away_team?.name || 'TBD' }}
                                </span>
                                <div class="size-2 rounded-full bg-red-500"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    matches: {
        type: Array,
        default: () => []
    }
});

const getWeekMatches = (week) => {
    return props.matches.filter(m => m.week === week);
}

const isWeekPlayed = (week) => {
    const weekMatches = getWeekMatches(week);
    return weekMatches.length > 0 && weekMatches.every(m => m.is_played);
}
</script>