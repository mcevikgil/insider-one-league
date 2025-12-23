<template>
    <div class="bg-white dark:bg-surface-dark rounded-xl border border-gray-200 dark:border-border-dark p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 text-primary">
            <span class="material-symbols-outlined">edit_note</span>
            <h3 class="text-lg font-bold uppercase tracking-wide">Select Teams</h3>
        </div>

        <div class="mb-4 text-sm text-slate-500 dark:text-slate-400">
            Selected: <span class="font-bold text-primary">{{ selectedCount }}/4</span>
        </div>

        <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2">
            <div v-for="team in teams" :key="team.id" @click="toggleSelect(team)" :class="[
                'flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all border',
                team.is_selected
                    ? 'bg-primary/10 border-primary'
                    : 'bg-slate-50 dark:bg-[#0b0d11] border-gray-200 dark:border-border-dark hover:border-primary/50'
            ]">
                <div class="flex items-center gap-3">
                    <div
                        class="size-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                        {{ team.short_name }}
                    </div>
                    <div>
                        <span class="font-medium dark:text-white">{{ team.name }}</span>
                        <div class="text-xs text-slate-400">Strength: {{ team.strength }}</div>
                    </div>
                </div>
                <div v-if="team.is_selected" class="text-primary">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3">
            <button @click="$emit('generate')" :disabled="selectedCount !== 4" :class="[
                'w-full flex items-center justify-center gap-2 font-bold py-3.5 px-6 rounded-lg transition-all',
                selectedCount === 4
                    ? 'bg-primary hover:bg-blue-600 text-white shadow-lg shadow-blue-500/20 active:scale-[0.98]'
                    : 'bg-slate-200 dark:bg-slate-700 text-slate-400 cursor-not-allowed'
            ]">
                <span class="material-symbols-outlined">bolt</span>
                <span>Generate Fixtures</span>
            </button>
            <button @click="$emit('reset')"
                class="w-full flex items-center justify-center gap-2 bg-transparent hover:bg-slate-100 dark:hover:bg-white/5 text-slate-600 dark:text-slate-400 font-semibold py-2.5 px-6 rounded-lg transition-all">
                <span class="material-symbols-outlined text-lg">restart_alt</span>
                <span>Reset</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    teams: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['toggle', 'generate', 'reset']);

const selectedCount = computed(() => {
    return props.teams.filter(t => t.is_selected).length;
});

const toggleSelect = (team) => {
    if (!team.is_selected && selectedCount.value >= 4) {
        return;
    }
    emit('toggle', team.id);
}

</script>