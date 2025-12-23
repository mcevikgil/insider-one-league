<template>
    <div
        class="bg-white dark:bg-surface-dark rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-surface-dark-lighter sticky top-0 z-10">
                    <tr>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center">#
                        </th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Club</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-10">P
                        </th>
                        <th
                            class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-10 hidden sm:table-cell">
                            W</th>
                        <th
                            class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-10 hidden sm:table-cell">
                            D</th>
                        <th
                            class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-10 hidden sm:table-cell">
                            L</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-14">GD
                        </th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-14">PTS
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    <tr v-for="(team, index) in standings" :key="team.team_id"
                        class="hover:bg-slate-50 dark:hover:bg-surface-dark-lighter transition-colors">
                        <td class="p-4 text-center">
                            <span :class="[
                                'inline-flex items-center justify-center size-6 rounded font-bold text-xs',
                                index === 0 ? 'bg-primary/10 text-primary' : 'text-slate-500'
                            ]">
                                {{ index + 1 }}
                            </span>
                        </td>
                        <td class="p-4 font-medium dark:text-white">
                            <div class="flex items-center gap-3">
                                <div
                                    class="size-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                                    {{ team.short_name }}
                                </div>
                                <span class="truncate">{{ team.team_name }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-center text-slate-500 dark:text-slate-400">{{ team.played }}</td>
                        <td class="p-4 text-center text-slate-500 dark:text-slate-400 hidden sm:table-cell">{{ team.won
                        }}</td>
                        <td class="p-4 text-center text-slate-500 dark:text-slate-400 hidden sm:table-cell">{{
                            team.drawn }}</td>
                        <td class="p-4 text-center text-slate-500 dark:text-slate-400 hidden sm:table-cell">{{ team.lost
                        }}</td>
                        <td class="p-4 text-center font-medium"
                            :class="team.goal_difference >= 0 ? 'text-green-500' : 'text-red-500'">
                            {{ team.goal_difference >= 0 ? '+' : '' }}{{ team.goal_difference }}
                        </td>
                        <td class="p-4 text-center font-bold dark:text-white">{{ team.points }}</td>
                    </tr>
                    <tr v-if="standings.length === 0">
                        <td colspan="8" class="p-8 text-center text-slate-400">
                            No standings data yet
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
defineProps({
    standings: {
        type: Array,
        default: () => []
    }
});
</script>