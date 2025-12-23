<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Team;


class SimulationService
{
    private const HOME_ADVANTAGE = 8;
    public function simulateMatch(Game $game): Game
    {
        if ($game->is_played) {
            return $game;
        }

        $homeTeam = $game->homeTeam;
        $awayTeam = $game->awayTeam;

        $scores = $this->calculateScore($homeTeam, $awayTeam);

        $game->home_score = $scores['home'];
        $game->away_score = $scores['away'];
        $game->is_played = true;
        $game->save();

        return $game;
    }

    public function simulateWeek(int $week): array
    {
        $games = Game::where('week', $week)->where('is_played', false)->get();
        $result = [];

        foreach ($games as $game) {
            $result[] = $this->simulateMatch($game);
        }

        return $result;
    }

    public function simulateAll(): array
    {
        $games = Game::where('is_played', false)->orderBy('week')->get();
        $result = [];
        foreach ($games as $game) {
            $result[] = $this->simulateMatch($game);
        }
        return $result;
    }

    private function calculateScore(Team $home, Team $away): array
    {

        $homeOffense = ($home->attack * 0.6) + ($home->form * 0.25) + ($home->squad_depth * 0.15);
        $awayDefense = ($away->attack * 0.6) + ($away->form * 0.25) + ($away->squad_depth * 0.15);
        $awayOffense = ($away->attack * 0.6) + ($away->form * 0.25) + ($away->squad_depth * 0.15);
        $homeDefense = ($home->attack * 0.6) + ($home->form * 0.25) + ($home->squad_depth * 0.15);

        $homeExpected = ($homeOffense - $awayDefense + 50 + self::HOME_ADVANTAGE) / 50;
        $awayExpected = ($awayOffense - $homeDefense + 50) / 50;

        $homeExpected = max(0.5, min(3.5, $homeExpected));
        $awayExpected = max(0.3, min(3.0, $awayExpected));

        return ['home' => $this->poissonRandom($homeExpected), 'away' => $this->poissonRandom($awayExpected)];
    }

    private function poissonRandom(float $lambda): int
    {
        $l = exp(-$lambda);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= mt_rand() / mt_getrandmax();
        } while ($p > $l);

        return $k - 1;
    }

    /**
     * Monte Carlo Simülasyonu
     */
    public function getMatchProbability(Team $home, Team $away): array
    {
        $simulations = 1000;
        $homeWins = 0;
        $draws = 0;
        $awayWins = 0;

        for ($i = 0; $i < $simulations; $i++) {
            $score = $this->calculateScore($home, $away);

            if ($score['home'] > $score['away']) {
                $homeWins++;
            } elseif ($score['home'] < $score['away']) {
                $awayWins++;
            } else {
                $draws++;
            }
        }

        return [
            'home_win' => round(($homeWins / $simulations) * 100, 1),
            'draw' => round(($draws / $simulations) * 100, 1),
            'away_win' => round(($awayWins / $simulations) * 100, 1),
        ];
    }
}
