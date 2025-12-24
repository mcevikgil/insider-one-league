<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\SimulationService;
use ReflectionClass;

class SimulationServiceTest extends TestCase
{
    private SimulationService $service;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SimulationService();
        $this->reflection = new ReflectionClass($this->service);
    }

    /**
     * Test Poisson random generates non-negative integers
     */
    public function test_poisson_random_returns_non_negative_integer(): void
    {
        $method = $this->reflection->getMethod(name: 'poissonRandom');

        for ($i = 0; $i < 100; $i++) {
            $result = $method->invoke($this->service, 1.5);

            $this->assertIsInt($result);
            $this->assertGreaterThanOrEqual(0, $result);
        }
    }

    /**
     * Test Poisson average is close to lambda over many iterations
     */
    public function test_poisson_random_average_approximates_lambda(): void
    {
        $method = $this->reflection->getMethod('poissonRandom');

        $lambda = 2.0;
        $iterations = 1000;
        $sum = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $sum += $method->invoke($this->service, $lambda);
        }

        $average = $sum / $iterations;

        // Average should be within 20% of lambda
        $this->assertEqualsWithDelta($lambda, $average, 0.4);
    }

    /**
     * Test calculate score returns valid structure
     */
    public function test_calculate_score_returns_array_with_home_and_away(): void
    {
        $homeTeam = $this->createMockTeam(85, 80, 82, 78);
        $awayTeam = $this->createMockTeam(75, 70, 72, 68);

        $method = $this->reflection->getMethod('calculateScore');
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
     * Test stronger team scores more on average
     */
    public function test_stronger_team_scores_more_on_average(): void
    {
        $strongTeam = $this->createMockTeam(95, 92, 90, 88);
        $weakTeam = $this->createMockTeam(60, 58, 55, 52);

        $method = $this->reflection->getMethod('calculateScore');

        $strongScoreSum = 0;
        $weakScoreSum = 0;
        $iterations = 500;

        for ($i = 0; $i < $iterations; $i++) {
            $result = $method->invoke($this->service, $strongTeam, $weakTeam);
            $strongScoreSum += $result['home'];
            $weakScoreSum += $result['away'];
        }

        $strongAvg = $strongScoreSum / $iterations;
        $weakAvg = $weakScoreSum / $iterations;

        $this->assertGreaterThan($weakAvg, $strongAvg);
    }

    /**
     * Test home advantage increases home team expected goals
     */
    public function test_home_advantage_effect(): void
    {
        // Same strength teams
        $teamA = $this->createMockTeam(80, 80, 80, 80);
        $teamB = $this->createMockTeam(80, 80, 80, 80);

        $method = $this->reflection->getMethod('calculateScore');

        $homeScoreSum = 0;
        $awayScoreSum = 0;
        $iterations = 500;

        for ($i = 0; $i < $iterations; $i++) {
            $result = $method->invoke($this->service, $teamA, $teamB);
            $homeScoreSum += $result['home'];
            $awayScoreSum += $result['away'];
        }

        // Home team should score slightly more due to home advantage
        $this->assertGreaterThan($awayScoreSum, $homeScoreSum);
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
