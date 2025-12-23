<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>

        <div
            class="relative bg-white dark:bg-surface-dark rounded-xl border border-gray-200 dark:border-border-dark shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-slate-50 dark:bg-[#15181e] px-6 py-4 border-b border-gray-200 dark:border-border-dark">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold dark:text-white">Edit Match Result</h3>
                    <button @click="$emit('close')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Week {{ match?.week }}</p>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 text-center">
                        <div
                            class="size-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold mx-auto mb-2">
                            {{ match?.home_team?.short_name }}
                        </div>
                        <p class="text-sm font-medium dark:text-white truncate">{{ match?.home_team?.name }}</p>
                        <input v-model.number="homeScore" type="number" min="0" max="20"
                            class="mt-3 w-20 mx-auto block text-center text-2xl font-bold py-2 bg-slate-100 dark:bg-[#0b0d11] border border-gray-200 dark:border-border-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-primary dark:text-white" />
                    </div>

                    <div class="text-2xl font-bold text-slate-300 dark:text-slate-600">
                        -
                    </div>

                    <div class="flex-1 text-center">
                        <div
                            class="size-12 rounded-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white text-sm font-bold mx-auto mb-2">
                            {{ match?.away_team?.short_name }}
                        </div>
                        <p class="text-sm font-medium dark:text-white truncate">{{ match?.away_team?.name }}</p>
                        <input v-model.number="awayScore" type="number" min="0" max="20"
                            class="mt-3 w-20 mx-auto block text-center text-2xl font-bold py-2 bg-slate-100 dark:bg-[#0b0d11] border border-gray-200 dark:border-border-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-primary dark:text-white" />
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-slate-50 dark:bg-[#15181e] border-t border-gray-200 dark:border-border-dark flex gap-3">
                <button @click="$emit('close')"
                    class="flex-1 py-2.5 px-4 rounded-lg border border-gray-200 dark:border-border-dark text-slate-600 dark:text-slate-400 font-semibold hover:bg-slate-100 dark:hover:bg-white/5 transition-colors">
                    Cancel
                </button>
                <button @click="handleSave" :disabled="loading" class="flex-1 py-2.5 px-4 rounded-lg bg-primary hover:bg-blue-600 text-white font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center 

                gap-2">
                    <span v-if="loading" class="material-symbols-outlined animate-spin text-lg">refresh</span>
                    <span>{{ loading ? 'Saving...' : 'Save Result' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    match: {
        type: Object,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'save']);

const homeScore = ref(0);
const awayScore = ref(0);

// Watch for match changes to update scores
watch(() => props.match, (newMatch) => {
    if (newMatch) {
        homeScore.value = newMatch.home_score ?? 0;
        awayScore.value = newMatch.away_score ?? 0;
    }
}, { immediate: true });

const handleSave = () => {
    emit('save', {
        matchId: props.match.id,
        homeScore: homeScore.value,
        awayScore: awayScore.value
    });
};
</script>