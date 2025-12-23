<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LeagueService;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;
use App\Models\Game;

class LeagueController extends Controller
{
    public function __construct(private LeagueService $leagueService, private SimulationService $simulationService) {}

    public function generateFixtures(): JsonResponse
    {
        $result = $this->leagueService->generateFixture();
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function currentWeek(): JsonResponse
    {
        return response()->json([
            'current_week' => $this->leagueService->getCurrentWeek()
        ]);
    }

    public function reset(): JsonResponse
    {
        $this->leagueService->reset();

        return response()->json(['message' => 'League reset successfully']);
    }

    public function matches(): JsonResponse
    {
        return response()->json([
            'matches' => $this->leagueService->getMatches()
        ]);
    }

    public function matchesByWeek(int $week): JsonResponse
    {
        return response()->json([
            'week' => $week,
            'matches' => $this->leagueService->getMatchesByWeek($week)
        ]);
    }

    public function simulateWeek(): JsonResponse
    {
        $currentWeek = $this->leagueService->getCurrentWeek();
        $result = $this->simulationService->simulateWeek($currentWeek);

        return response()->json([
            'week' => $currentWeek,
            'matches' => $result,
            'message' => "Week {$currentWeek} simulated successfully"
        ]);
    }

    public function simulateAll(): JsonResponse
    {
        $result = $this->simulationService->simulateAll();
        return response()->json([
            'matches' => $result,
            'message' => 'All matched simulated successfully'
        ]);
    }
    public function updateMatch(Request $request, Game $game): JsonResponse
    {
        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $updatedGame = $this->leagueService->updateMatch(
            $game,
            $validated['home_score'],
            $validated['away_score']
        );

        return response()->json([
            'game' => $updatedGame,
            'message' => 'Match result updated successfully'
        ]);
    }
}
