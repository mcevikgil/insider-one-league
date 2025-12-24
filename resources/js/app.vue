<template>
    <div
        class="bg-background-light dark:bg-background-dark min-h-screen w-full flex flex-col font-display text-slate-900 dark:text-white">
        <header
            class="w-full border-b border-gray-200 dark:border-border-dark bg-white dark:bg-[#111318] sticky top-0 z-50">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center h-16">
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Insider One Champions League
                    </h1>
                </div>
            </div>
        </header>

        <main class="flex-1 w-full max-w-[1600px] mx-auto p-4 sm:p-6 lg:p-8">
            <div v-if="phase === 'selection' || phase === 'fixtures'"
                class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <section class="lg:col-span-4 xl:col-span-3">
                    <TeamSelection :teams="teams" @toggle="handleToggleTeam" @generate="handleGenerateFixtures"
                        @reset="handleReset" />
                </section>
                <section class="lg:col-span-8 xl:col-span-9">
                    <FixtureDisplay :matches="matches" />
                </section>
            </div>

            <div v-else class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <div class="xl:col-span-5 flex flex-col gap-4">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">League Standings</h2>
                    <LeagueTable :standings="standings" />
                </div>

                <div class="xl:col-span-4 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Match Week</h2>
                        <!-- Hafta Seçici -->
                        <div class="flex items-center gap-2">
                            <button v-for="week in 6" :key="week" @click="displayWeek = week" :class="[
                                'size-8 rounded-lg text-sm font-bold transition-all',
                                displayWeek === week
                                    ? 'bg-primary text-white'
                                    : isWeekComplete(week)
                                        ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200'
                                        : 'bg-slate-100 dark:bg-surface-dark-lighter text-slate-500 hover:bg-slate-200'
                            ]">
                                {{ week }}
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div v-for="match in displayWeekMatches" :key="match.id" @click="openEditModal(match)"
                            class="bg-white dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between cursor-pointer hover:border-primary/50 transition-all">
                            <div class="flex items-center gap-3 w-1/3">
                                <div
                                    class="size-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ match.home_team?.short_name }}
                                </div>
                                <span class="text-sm font-semibold dark:text-white truncate">{{ match.home_team?.name
                                }}</span>
                            </div>
                            <div class="flex flex-col items-center justify-center w-1/3">
                                <span :class="[
                                    'px-3 py-1 rounded text-sm font-mono font-bold',
                                    match.is_played ? 'bg-primary/10 text-primary' : 'bg-slate-100 dark:bg-surface-dark-lighter dark:text-white'
                                ]">
                                    {{ match.is_played ? `${match.home_score} - ${match.away_score}` : 'vs' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 w-1/3 justify-end">
                                <span class="text-sm font-semibold dark:text-white truncate">{{ match.away_team?.name
                                }}</span>
                                <div
                                    class="size-8 rounded-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ match.away_team?.short_name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-3 flex flex-col gap-4">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Championship Prodictions</h2>
                    <PredictionPanel :predictions="predictions" />
                </div>
            </div>
        </main>

        <MatchEditModal :show="showEditModal" :match="selectedMatch" :loading="loading" @close="closeEditModal"
            @save="handleUpdateMatch" />
        <ControlButtons v-if="hasFixtures" :current-week="currentWeek" :all-played="allMatchesPlayed"
            :show-play-buttons="phase == 'simulation'" :loading="loading" @simulate-week="handleSimulateWeek"
            @simulate-all="handleSimulateAll" @reset="handleReset" @start-simulate="handleStartSimulate" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useApi } from './composables/useApi';
import TeamSelection from './components/TeamSelection.vue';
import FixtureDisplay from './components/FixtureDisplay.vue';
import LeagueTable from './components/LeagueTable.vue';
import PredictionPanel from './components/PredictionPanel.vue';
import ControlButtons from './components/ControlButtons.vue';
import MatchEditModal from './components/MatchEditModal.vue';

const {
    getTeams,
    toggleTeamSelect,
    generateFixtures,
    getMatches,
    getStandings,
    getPredictions,
    getCurrentWeek,
    simulateWeek,
    simulateAll,
    resetLeague,
    updateMatch
} = useApi();

// State
const teams = ref([]);
const matches = ref([]);
const standings = ref([]);
const predictions = ref([]);
const currentWeek = ref(1);
const loading = ref(false);
const phase = ref('selection');
const selectedMatch = ref(null);
const showEditModal = ref(false);
const displayWeek = ref(1);

// Computed
const hasFixtures = computed(() => matches.value.length > 0);

const showSimulation = computed(() => phase.value === 'simulation');

const allMatchesPlayed = computed(() => {
    return matches.value.length > 0 && matches.value.every(m => m.is_played);
});

const currentWeekMatches = computed(() => {
    return matches.value.filter(m => m.week === currentWeek.value);
});

const isCurrentWeekPlayed = computed(() => {
    return currentWeekMatches.value.length > 0 && currentWeekMatches.value.every(m => m.is_played);
});

const displayWeekMatches = computed(() => {
    return matches.value.filter(m => m.week === displayWeek.value);
});

const isDisplayWeekPlayed = computed(() => {
    return displayWeekMatches.value.length > 0 && displayWeekMatches.value.every(m => m.is_played);
});

// Methods
const isWeekComplete = (week) => {
    const weekMatches = matches.value.filter(m => m.week === week);
    return weekMatches.length > 0 && weekMatches.every(m => m.is_played);
};
const loadTeams = async () => {
    const data = await getTeams();
    teams.value = data.teams;
};

const loadMatches = async () => {
    const data = await getMatches();
    matches.value = data.matches;
};

const loadStandings = async () => {
    const data = await getStandings();
    standings.value = data.standings;
};

const loadPredictions = async () => {
    if (currentWeek.value >= 4) {
        const data = await getPredictions();
        predictions.value = data.predictions;
    } else { return; }
};

const loadCurrentWeek = async () => {
    const data = await getCurrentWeek();
    currentWeek.value = data.current_week;
};

const openEditModal = (match) => {
    selectedMatch.value = match;
    showEditModal.value = true;
};

const closeEditModal = () => {
    selectedMatch.value = null;
    showEditModal.value = false;
};

const handleUpdateMatch = async ({ matchId, homeScore, awayScore }) => {
    loading.value = true;
    try {
        await updateMatch(matchId, homeScore, awayScore);
        await refreshAll();
        closeEditModal();
    } finally {
        loading.value = false;
    }
};

const refreshAll = async () => {
    await Promise.all([
        loadMatches(),
        loadStandings(),
        loadPredictions(),
        loadCurrentWeek()
    ]);
};

const handleToggleTeam = async (teamId) => {
    await toggleTeamSelect(teamId);
    await loadTeams();
};

const handleGenerateFixtures = async () => {
    loading.value = true;
    try {
        await generateFixtures();
        await loadMatches();
        phase.value = 'fixtures';
    } finally {
        loading.value = false;
    }
};

const handleSimulateWeek = async () => {
    loading.value = true;
    try {
        await simulateWeek();
        phase.value = 'simulation';
        displayWeek.value = currentWeek.value;
        await refreshAll();
    } finally {
        loading.value = false;
    }
};

const handleSimulateAll = async () => {
    loading.value = true;
    try {
        await simulateAll();
        phase.value = 'simulation';
        await refreshAll();
    } finally {
        loading.value = false;
    }
};

const handleReset = async () => {
    loading.value = true;
    try {
        await resetLeague();
        matches.value = [];
        standings.value = [];
        predictions.value = [];
        currentWeek.value = 1;
        phase.value = 'selection';
        await loadTeams();
    } finally {
        loading.value = false;
    }
};

const handleStartSimulate = async (params) => {
    phase.value = 'simulation';
    await refreshAll();
}

// Lifecycle
onMounted(async () => {
    await loadTeams();
    await loadMatches();
    if (hasFixtures.value) {
        await refreshAll();
    }
});
</script>