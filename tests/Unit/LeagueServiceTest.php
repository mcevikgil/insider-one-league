<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LeagueServiceTest extends TestCase
{
    /**
     * Test round-robin algorithm generates correct number of matches
     * 4 teams = 6 matches per half (3 rounds x 2 matches) x 2 halves = 12 total
     */
    public function test_round_robin_generates_correct_match_count(): void
    {
        $teamIds = [1, 2, 3, 4];
        $fixtures = $this->generateRoundRobinFixtures($teamIds);

        $this->assertCount(12, $fixtures);
    }

    /**
     * Test each team plays exactly 6 matches (home + away against each opponent)
     */
    public function test_each_team_plays_six_matches(): void
    {
        $teamIds = [1, 2, 3, 4];
        $fixtures = $this->generateRoundRobinFixtures($teamIds);

        foreach ($teamIds as $teamId) {
            $matchCount = 0;
            foreach ($fixtures as $match) {
                if ($match['home'] === $teamId || $match['away'] === $teamId) {
                    $matchCount++;
                }
            }
            $this->assertEquals(6, $matchCount, "Team {$teamId} should play 6 matches");
        }
    }

    /**
     * Test each team plays home and away against each opponent
     */
    public function test_each_pair_plays_home_and_away(): void
    {
        $teamIds = [1, 2, 3, 4];
        $fixtures = $this->generateRoundRobinFixtures($teamIds);

        // Check each pair plays both home and away
        $pairs = [
            [1, 2], [1, 3], [1, 4],
            [2, 3], [2, 4],
            [3, 4]
        ];

        foreach ($pairs as $pair) {
            $homeAwayFound = false;
            $awayHomeFound = false;

            foreach ($fixtures as $match) {
                if ($match['home'] === $pair[0] && $match['away'] === $pair[1]) {
                    $homeAwayFound = true;
                }
                if ($match['home'] === $pair[1] && $match['away'] === $pair[0]) {
                    $awayHomeFound = true;
                }
            }

            $this->assertTrue($homeAwayFound, "Team {$pair[0]} should play home against team {$pair[1]}");
            $this->assertTrue($awayHomeFound, "Team {$pair[1]} should play home against team {$pair[0]}");
        }
    }

    /**
     * Test fixtures are distributed across 6 weeks
     */
    public function test_fixtures_distributed_across_six_weeks(): void
    {
        $teamIds = [1, 2, 3, 4];
        $fixtures = $this->generateRoundRobinFixtures($teamIds);

        $weeks = array_unique(array_column($fixtures, 'week'));
        sort($weeks);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $weeks);
    }

    /**
     * Test each week has exactly 2 matches (4 teams / 2)
     */
    public function test_each_week_has_two_matches(): void
    {
        $teamIds = [1, 2, 3, 4];
        $fixtures = $this->generateRoundRobinFixtures($teamIds);

        for ($week = 1; $week <= 6; $week++) {
            $weekMatches = array_filter($fixtures, fn($m) => $m['week'] === $week);
            $this->assertCount(2, $weekMatches, "Week {$week} should have 2 matches");
        }
    }

    /**
     * Test no team plays twice in the same week
     */
    public function test_no_team_plays_twice_in_same_week(): void
    {
        $teamIds = [1, 2, 3, 4];
        $fixtures = $this->generateRoundRobinFixtures($teamIds);

        for ($week = 1; $week <= 6; $week++) {
            $weekMatches = array_filter($fixtures, fn($m) => $m['week'] === $week);
            $teamsInWeek = [];

            foreach ($weekMatches as $match) {
                $this->assertNotContains($match['home'], $teamsInWeek, "Team {$match['home']} plays twice in week {$week}");
                $this->assertNotContains($match['away'], $teamsInWeek, "Team {$match['away']} plays twice in week {$week}");
                $teamsInWeek[] = $match['home'];
                $teamsInWeek[] = $match['away'];
            }
        }
    }

    /**
     * Test second half reverses home/away from first half
     */
    public function test_second_half_reverses_home_away(): void
    {
        $teamIds = [1, 2, 3, 4];
        $fixtures = $this->generateRoundRobinFixtures($teamIds);

        $firstHalf = array_filter($fixtures, fn($m) => $m['week'] <= 3);
        $secondHalf = array_filter($fixtures, fn($m) => $m['week'] > 3);

        foreach ($firstHalf as $firstMatch) {
            $reversed = false;
            foreach ($secondHalf as $secondMatch) {
                if ($firstMatch['home'] === $secondMatch['away'] &&
                    $firstMatch['away'] === $secondMatch['home']) {
                    $reversed = true;
                    break;
                }
            }
            $this->assertTrue($reversed, "Match {$firstMatch['home']} vs {$firstMatch['away']} should have reverse in second half");
        }
    }

    /**
     * Helper: Generate round-robin fixtures (same algorithm as LeagueService)
     */
    private function generateRoundRobinFixtures(array $teamIds): array
    {
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

        $fixtures = [];
        $week = 1;

        // First half
        foreach ($rounds as $roundMatches) {
            foreach ($roundMatches as $match) {
                $fixtures[] = [
                    'week' => $week,
                    'home' => $match[0],
                    'away' => $match[1],
                ];
            }
            $week++;
        }

        // Second half (reversed home/away)
        foreach ($rounds as $roundMatches) {
            foreach ($roundMatches as $match) {
                $fixtures[] = [
                    'week' => $week,
                    'home' => $match[1],
                    'away' => $match[0],
                ];
            }
            $week++;
        }

        return $fixtures;
    }
}
