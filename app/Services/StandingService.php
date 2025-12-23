<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Game;
use Illuminate\Support\Collection;

class StandingService
{
    public function getStandings(): Collection
    {
        $teams = Team::where('is_selected', true)->get();
        $standings = $teams->map(function ($team) {
            $stats = $this->calculateTeamStats($team->id);

            return [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'short_name' => $team->short_name,
                'played' => $stats['played'],
                'won' => $stats['won'],
                'drawn' => $stats['drawn'],
                'lost' => $stats['lost'],
                'goals_for' => $stats['goals_for'],
                'goals_against' => $stats['goals_against'],
                'goal_difference' => $stats['goals_for'] - $stats['goals_against'],
                'points' => ($stats['won'] * 3) + $stats['drawn'],
            ];
        });

        return $standings->sort(function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            return $b['goals_for'] - $a['goals_for'];
        })->values();
    }

    private function calculateTeamStats(int $teamId): array
    {
        $homeGames = Game::where('home_team_id', $teamId)->where('is_played', true)->get();
        $awayGames = Game::where('away_team_id', $teamId)->where('is_played', true)->get();

        $stats = [
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
        ];

        foreach ($homeGames as $game) {
            $stats['played']++;
            $stats['goals_for'] += $game->home_score;
            $stats['goals_against'] += $game->away_score;

            if ($game->home_score > $game->away_score) {
                $stats['won']++;
            } elseif ($game->home_score < $game->away_score) {
                $stats['lost']++;
            } else {
                $stats['drawn']++;
            }
        }

        foreach ($awayGames as $game) {
            $stats['played']++;
            $stats['goals_for'] += $game->away_score;
            $stats['goals_against'] += $game->home_score;

            if ($game->away_score > $game->home_score) {
                $stats['won']++;
            } elseif ($game->away_score < $game->home_score) {
                $stats['lost']++;
            } else {
                $stats['drawn']++;
            }
        }

        return $stats;
    }
}
