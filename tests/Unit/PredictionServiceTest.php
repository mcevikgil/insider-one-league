<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PredictionService;
use ReflectionClass;

class PredictionServiceTest extends TestCase
{
    private PredictionService $service;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PredictionService();
        $this->reflection = new ReflectionClass($this->service);
    }

    /**
     * Test Poisson random generates non-negative integers
     */
    public function test_poisson_random_returns_non_negative_integer(): void
    {
        $method = $this->reflection->getMethod('poissonRandom');

        for ($i = 0; $i < 100; $i++) {
            $result = $method->invoke($this->service, 1.5);

            $this->assertIsInt($result);
            $this->assertGreaterThanOrEqual(0, $result);
        }
    }

    /**
     * Test Poisson distribution average approximates lambda
     */
    public function test_poisson_average_approximates_lambda(): void
    {
        $method = $this->reflection->getMethod('poissonRandom');

        $lambda = 2.5;
        $iterations = 1000;
        $sum = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $sum += $method->invoke($this->service, $lambda);
        }

        $average = $sum / $iterations;
        $this->assertEqualsWithDelta($lambda, $average, 0.5);
    }

    /**
     * Test simulate match returns valid score structure
     */
    public function test_simulate_match_returns_valid_structure(): void
    {
        $homeTeam = $this->createMockTeam(85, 80, 82, 78);
        $awayTeam = $this->createMockTeam(75, 70, 72, 68);

        $method = $this->reflection->getMethod('simulateMatch');
        $result = $method->invoke($this->service, $homeTeam, $awayTeam);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('home', $result);
        $this->assertArrayHasKey('away', $result);
        $this->assertIsInt($result['home']);
        $this->assertIsInt($result['away']);
        $this->assertGreaterThanOrEqual(0, $result['home']);
        $this->assertGreaterThanOrEqual(0, $result['away']);
    }

    /**
     * Test stronger team wins more often in simulations
     */
    public function test_stronger_team_wins_more_often(): void
    {
        $strongTeam = $this->createMockTeam(95, 90, 92, 88);
        $weakTeam = $this->createMockTeam(55, 50, 52, 48);

        $method = $this->reflection->getMethod('simulateMatch');

        $strongWins = 0;
        $weakWins = 0;
        $iterations = 500;

        for ($i = 0; $i < $iterations; $i++) {
            $result = $method->invoke($this->service, $strongTeam, $weakTeam);
            if ($result['home'] > $result['away']) {
                $strongWins++;
            } elseif ($result['away'] > $result['home']) {
                $weakWins++;
            }
        }

        $this->assertGreaterThan($weakWins, $strongWins);
    }

    /**
     * Test home advantage effect in predictions
     */
    public function test_home_advantage_in_predictions(): void
    {
        $teamA = $this->createMockTeam(80, 80, 80, 80);
        $teamB = $this->createMockTeam(80, 80, 80, 80);

        $method = $this->reflection->getMethod('simulateMatch');

        $homeWins = 0;
        $awayWins = 0;
        $iterations = 500;

        for ($i = 0; $i < $iterations; $i++) {
            $result = $method->invoke($this->service, $teamA, $teamB);
            if ($result['home'] > $result['away']) {
                $homeWins++;
            } elseif ($result['away'] > $result['home']) {
                $awayWins++;
            }
        }

        $this->assertGreaterThan($awayWins, $homeWins);
    }

    /**
     * Test expected goals clamping (min/max boundaries)
     */
    public function test_expected_goals_within_reasonable_range(): void
    {
        // Very weak team vs very strong team
        $weakTeam = $this->createMockTeam(30, 30, 30, 30);
        $strongTeam = $this->createMockTeam(99, 99, 99, 99);

        $method = $this->reflection->getMethod('simulateMatch');

        // Run many simulations
        $maxScore = 0;
        for ($i = 0; $i < 200; $i++) {
            $result = $method->invoke($this->service, $strongTeam, $weakTeam);
            $maxScore = max($maxScore, $result['home'], $result['away']);
        }

        // Scores should rarely exceed 7-8 due to Poisson distribution with clamped lambda
        $this->assertLessThanOrEqual(12, $maxScore);
    }

    /**
     * Helper: Create a mock team object
     */
    private function createMockTeam(int $attack, int $defense, int $form, int $squadDepth): object
    {
        return (object) [
            'attack' => $attack,
            'defense' => $defense,
            'form' => $form,
            'squad_depth' => $squadDepth,
        ];
    }
}
