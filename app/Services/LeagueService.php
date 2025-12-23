<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Game;


class LeagueService
{
    public function generateFixture()
    {
        $teams = Team::where('is_selected', true)->get();

        if ($teams->count() !== 4) {
            return ['success' => false, 'message' => 'Exaxtly 4 teams must be selected'];
        }

        Game::truncate();

        $teamIds = $teams->pluck('id')->toArray();
        $numTeams = count($teamIds);

        $rounds = [];
        $away = array_slice($teamIds, 1);
        $home = $teamIds[0];

        for ($round = 0; $round < $numTeams - 1; $round++) {
            $roundMatches = [];
            $roundMatches[] = [$home, $away[0]];

            for ($i = 1; $i < $numTeams / 2; $i++) {
                $roundMatches[] = [$away[$i], $away[$numTeams - 1 - $i]];
            }

            $rounds[] = $roundMatches;

            $last = array_pop($away);
            array_unshift($away, $last);
        }

        $week = 1;

        foreach ($rounds as $roundMatches) {
            foreach ($roundMatches as $match) {
                Game::create([
                    'week' => $week,
                    'home_team_id' => $match[0],
                    'away_team_id' => $match[1],
                    'home_score' => null,
                    'away_score' => null,
                    'is_played' => false,
                ]);
            }
            $week++;
        }

        foreach ($rounds as $roundMatches) {
            foreach ($roundMatches as $match) {
                Game::create([
                    'week' => $week,
                    'home_team_id' => $match[1],
                    'away_team_id' => $match[0],
                    'home_score' => null,
                    'away_score' => null,
                    'is_played' => false,
                ]);
            }
            $week++;
        }

        return ['success' => true, 'message' => 'Fixtures generated successfully'];
    }

    public function getCurrentWeek(): int
    {
        $game = Game::where('is_played', false)->orderBy('week')->first();
        return $game ? $game->week : 6;
    }

    public function reset()
    {
        Game::truncate();
        Team::query()->update(['is_selected' => false]);
    }

    public function getMatches()
    {
        return Game::with(['homeTeam', 'awayTeam'])->orderBy('week')->get();
    }

    public function getMatchesByWeek(int $week)
    {
        return Game::with(['homeTeam', 'awayTeam'])->where('week', $week)->get();
    }
    public function updateMatch(Game $game, int $homeScore, int $awayScore): Game
    {
        $game->home_score = $homeScore;
        $game->away_score = $awayScore;
        $game->is_played = true;
        $game->save();

        return $game->load(['homeTeam', 'awayTeam']);
    }
}
