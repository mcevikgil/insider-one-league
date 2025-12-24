<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Game;


class PredictionService
{
    private const SIMULATIONS = 1000;
    private const HOME_ADVANTAGE = 8;

    public function getChampionshipOdds(): array
    {
        $teams = Team::where('is_selected', true)->get();

        if ($teams->count() !== 4) {
            return [];
        }

        $championships = [];
        foreach ($teams as $team) {
            $championships[$team->id] = 0;
        }

        for ($i = 0; $i < self::SIMULATIONS; $i++) {
            $championId = $this->simulateRemainingSeasonAndGetChampion();
            if ($championId) {
                $championships[$championId]++;
            }
        }

        $result = [];
        foreach ($teams as $team) {
            $result[] = [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'short_name' => $team->short_name,
                'probability' => round(($championships[$team->id] / self::SIMULATIONS) * 100, 1),
            ];
        }

        usort($result, fn($a, $b) => $b['probability'] <=> $a['probability']);

        return $result;
    }

    private function simulateRemainingSeasonAndGetChampion(): ?int
    {
        $teams = Team::where('is_selected', operator: true)->get();
        $remainingGames = Game::where('is_played', false)->get();

        $points = [];
        $goalDiff = [];
        foreach ($teams as $team) {
            $points[$team->id] = 0;
            $goalDiff[$team->id] = 0;
        }

        $playedGames = Game::where('is_played', true)->get();
        foreach ($playedGames as $game) {
            $this->addGamePoints($game, $points, $goalDiff, $game->home_score, $game->away_score);
        }

        foreach ($remainingGames as $game) {
            $homeTeam = $teams->firstWhere('id', $game->home_team_id);
            $awayTeam = $teams->firstWhere('id', $game->away_team_id);

            $score = $this->simulateMatch($homeTeam, $awayTeam);
            $this->addGamePoints($game, $points, $goalDiff, $score['home'], $score['away']);
        }

        $maxPoints = max($points);
        $candidates = array_keys(array_filter($points, fn($p) => $p === $maxPoints));

        if (count($candidates) === 1) {
            return $candidates[0];
        }

        $bestGD = PHP_INT_MIN;
        $champion = null;
        foreach ($candidates as $teamId) {
            if ($goalDiff[$teamId] > $bestGD) {
                $bestGD = $goalDiff[$teamId];
                $champion = $teamId;
            }
        }

        return $champion;
    }

    private function addGamePoints(Game $game, array &$points, array &$goalDiff, int $homeScore, int $awayScore): void
    {
        if ($homeScore > $awayScore) {
            $points[$game->home_team_id] += 3;
        } elseif ($homeScore < $awayScore) {
            $points[$game->away_team_id] += 3;
        } else {
            $points[$game->home_team_id] += 1;
            $points[$game->away_team_id] += 1;
        }

        $goalDiff[$game->home_team_id] += ($homeScore - $awayScore);
        $goalDiff[$game->away_team_id] += ($awayScore - $homeScore);
    }

    private function simulateMatch(Team $home, Team $away): array
    {
        $homeOffense = ($home->attack * 0.6) + ($home->form * 0.25) + ($home->squad_depth * 0.15);
        $awayDefense = ($away->defense * 0.6) + ($away->form * 0.25) + ($away->squad_depth * 0.15);
        $awayOffense = ($away->attack * 0.6) + ($away->form * 0.25) + ($away->squad_depth * 0.15);
        $homeDefense = ($home->defense * 0.6) + ($home->form * 0.25) + ($home->squad_depth * 0.15);

        $homeExpected = ($homeOffense - $awayDefense + 50 + self::HOME_ADVANTAGE) / 50;
        $awayExpected = ($awayOffense - $homeDefense + 50) / 50;

        $homeExpected = max(0.5, min(3.5, $homeExpected));
        $awayExpected = max(0.3, min(3.0, $awayExpected));

        return [
            'home' => $this->poissonRandom($homeExpected),
            'away' => $this->poissonRandom($awayExpected),
        ];
    }

    private function poissonRandom(float $lambda): int
    {
        $L = exp(-$lambda);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= mt_rand() / mt_getrandmax();
        } while ($p > $L);

        return $k - 1;
    }
}
