<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StandingCalculationTest extends TestCase
{
    /**
     * Test points calculation: Win = 3 points
     */
    public function test_win_gives_3_points(): void
    {
        $points = $this->calculatePoints(wins: 1, draws: 0);
        $this->assertEquals(3, $points);
    }

    /**
     * Test points calculation: Draw = 1 point
     */
    public function test_draw_gives_1_point(): void
    {
        $points = $this->calculatePoints(wins: 0, draws: 1);
        $this->assertEquals(1, $points);
    }

    /**
     * Test points calculation: Loss = 0 points
     */
    public function test_loss_gives_0_points(): void
    {
        $points = $this->calculatePoints(wins: 0, draws: 0);
        $this->assertEquals(0, $points);
    }

    /**
     * Test points calculation: Multiple results
     */
    public function test_multiple_results_calculate_correctly(): void
    {
        // 3 wins, 2 draws, 1 loss = 3*3 + 2*1 + 0 = 11
        $points = $this->calculatePoints(wins: 3, draws: 2);
        $this->assertEquals(11, $points);
    }

    /**
     * Test goal difference calculation
     */
    public function test_goal_difference_calculation(): void
    {
        $goalsFor = 10;
        $goalsAgainst = 4;

        $goalDifference = $goalsFor - $goalsAgainst;

        $this->assertEquals(6, $goalDifference);
    }

    /**
     * Test negative goal difference
     */
    public function test_negative_goal_difference(): void
    {
        $goalsFor = 3;
        $goalsAgainst = 8;

        $goalDifference = $goalsFor - $goalsAgainst;

        $this->assertEquals(-5, $goalDifference);
    }

    /**
     * Test sorting: Points take priority
     */
    public function test_sorting_by_points_first(): void
    {
        $teamA = ['points' => 10, 'goal_difference' => 2];
        $teamB = ['points' => 8, 'goal_difference' => 5];

        $sorted = $this->sortStandings([$teamA, $teamB]);

        $this->assertEquals(10, $sorted[0]['points']);
    }

    /**
     * Test sorting: Goal difference as tiebreaker
     */
    public function test_sorting_by_goal_difference_when_points_equal(): void
    {
        $teamA = ['points' => 10, 'goal_difference' => 2, 'name' => 'A'];
        $teamB = ['points' => 10, 'goal_difference' => 5, 'name' => 'B'];

        $sorted = $this->sortStandings([$teamA, $teamB]);

        $this->assertEquals('B', $sorted[0]['name']);
    }

    /**
     * Helper: Calculate points
     */
    private function calculatePoints(int $wins, int $draws): int
    {
        return ($wins * 3) + ($draws * 1);
    }

    /**
     * Helper: Sort standings
     */
    private function sortStandings(array $standings): array
    {
        usort($standings, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            return $b['goal_difference'] - $a['goal_difference'];
        });
        return $standings;
    }
}
