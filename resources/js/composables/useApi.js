import axios from "axios";
import { ref } from "vue";

const api = axios.create({
    baseURL: "/api",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

export function useApi() {
    const loading = ref(false);
    const error = ref(null);

    const getTeams = async () => {
        loading.value = true;
        try {
            const response = await api.get("/teams");
            return response.data;
        } catch (err) {
            error.value = err.message;
        } finally {
            loading.value = false;
        }
    };

    const toggleTeamSelect = async (teamId) => {
        const response = await api.put(`/teams/${teamId}/toggle-select`);
        return response.data;
    };

    const generateFixtures = async () => {
        const response = await api.post("/league/generate-fixtures");
        return response.data;
    };

    const getMatches = async () => {
        const response = await api.get("/league/matches");
        return response.data;
    };
    const updateMatch = async (matchId, homeScore, awayScore) => {
        const response = await api.put(`/league/matches/${matchId}`, {
            home_score: homeScore,
            away_score: awayScore,
        });
        return response.data;
    };

    const simulateWeek = async () => {
        const response = await api.post("/league/simulate-week");
        return response.data;
    };

    const simulateAll = async () => {
        const response = await api.post("/league/simulate-all");
        return response.data;
    };

    const resetLeague = async () => {
        const response = await api.post("/league/reset");
        return response.data;
    };

    const getCurrentWeek = async () => {
        const response = await api.get("/league/current-week");
        return response.data;
    };

    const getStandings = async () => {
        const response = await api.get("/standings");
        return response.data;
    };

    const getPredictions = async () => {
        const response = await api.get("/predictions");
        return response.data;
    };

    return {
        loading,
        error,
        getTeams,
        toggleTeamSelect,
        generateFixtures,
        getMatches,
        simulateWeek,
        simulateAll,
        resetLeague,
        getCurrentWeek,
        getStandings,
        getPredictions,
        updateMatch
    };
}
