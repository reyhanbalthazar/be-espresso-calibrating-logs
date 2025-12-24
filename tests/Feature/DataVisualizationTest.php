<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\CoffeeShop;
use App\Models\Bean;
use App\Models\Grinder;
use App\Models\CalibrationSession;
use App\Models\Shot;

class DataVisualizationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $coffeeShop;
    protected $bean;
    protected $grinder;
    protected $session1;
    protected $session2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a coffee shop
        $this->coffeeShop = CoffeeShop::factory()->create();

        // Create a user associated with the coffee shop
        $this->user = User::factory()->create([
            'coffee_shop_id' => $this->coffeeShop->id
        ]);

        // Create a bean and grinder
        $this->bean = Bean::factory()->create([
            'coffee_shop_id' => $this->coffeeShop->id
        ]);
        $this->grinder = Grinder::factory()->create([
            'coffee_shop_id' => $this->coffeeShop->id
        ]);

        // Create calibration sessions
        $this->session1 = CalibrationSession::factory()->create([
            'bean_id' => $this->bean->id,
            'grinder_id' => $this->grinder->id,
            'user_id' => $this->user->id,
            'coffee_shop_id' => $this->coffeeShop->id
        ]);

        $this->session2 = CalibrationSession::factory()->create([
            'bean_id' => $this->bean->id,
            'grinder_id' => $this->grinder->id,
            'user_id' => $this->user->id,
            'coffee_shop_id' => $this->coffeeShop->id
        ]);

        // Create sample shots for session 1
        Shot::create([
            'calibration_session_id' => $this->session1->id,
            'shot_number' => 1,
            'grind_setting' => '12',
            'dose' => 18.0,
            'yield' => 36.0,
            'time_seconds' => 25,
            'taste_notes' => 'Balanced flavor',
            'action_taken' => 'None'
        ]);

        Shot::create([
            'calibration_session_id' => $this->session1->id,
            'shot_number' => 2,
            'grind_setting' => '12.5',
            'dose' => 18.0,
            'yield' => 38.0,
            'time_seconds' => 27,
            'taste_notes' => 'Slightly stronger',
            'action_taken' => 'Increased grind'
        ]);

        // Create sample shots for session 2
        Shot::create([
            'calibration_session_id' => $this->session2->id,
            'shot_number' => 1,
            'grind_setting' => '11.5',
            'dose' => 18.2,
            'yield' => 35.5,
            'time_seconds' => 24,
            'taste_notes' => 'Light extraction',
            'action_taken' => 'Decreased grind'
        ]);

        Shot::create([
            'calibration_session_id' => $this->session2->id,
            'shot_number' => 2,
            'grind_setting' => '12',
            'dose' => 18.0,
            'yield' => 36.5,
            'time_seconds' => 26,
            'taste_notes' => 'Better balance',
            'action_taken' => 'Slight adjustment'
        ]);
    }

    public function test_extraction_trends_endpoint(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/visualization/extraction-trends');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'summary' => [
                    'avg_extraction_yield',
                    'avg_extraction_ratio',
                    'avg_flow_rate',
                    'total_shots'
                ]
            ]);

        // Check that the response contains data
        $responseData = $response->json();
        $this->assertIsArray($responseData['data']);
        $this->assertGreaterThan(0, count($responseData['data']));
    }

    public function test_extraction_trends_endpoint_with_bean_filter(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/visualization/extraction-trends?bean_id={$this->bean->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'summary' => [
                    'avg_extraction_yield',
                    'avg_extraction_ratio',
                    'avg_flow_rate',
                    'total_shots'
                ]
            ]);
    }

    public function test_comparative_analysis_endpoint(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/visualization/comparative-analysis');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'summary' => [
                    'total_sessions',
                    'avg_session_shots',
                    'overall_avg_extraction_yield',
                    'overall_avg_extraction_ratio'
                ]
            ]);

        // Check that the response contains data
        $responseData = $response->json();
        $this->assertIsArray($responseData['data']);
        $this->assertGreaterThan(0, count($responseData['data']));
    }

    public function test_comparative_analysis_endpoint_with_session_ids(): void
    {
        $sessionIds = [$this->session1->id, $this->session2->id];
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/visualization/comparative-analysis?session_ids[]=' . $this->session1->id . '&session_ids[]=' . $this->session2->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'summary'
            ]);
    }

    public function test_optimal_parameters_endpoint(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/visualization/optimal-parameters');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'optimal_parameters',
                'recommendations',
                'summary' => [
                    'total_shots',
                    'optimal_shots_count',
                    'optimal_shots_percentage',
                    'avg_extraction_yield',
                    'avg_extraction_ratio',
                    'avg_flow_rate'
                ]
            ]);

        // Check that the response contains recommendations
        $responseData = $response->json();
        $this->assertIsArray($responseData['recommendations']);
    }

    public function test_summary_stats_endpoint(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/visualization/summary-stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_shots',
                'optimal_shots',
                'optimal_percentage',
                'avg_extraction_yield',
                'avg_extraction_ratio',
                'avg_flow_rate'
            ]);
    }

    public function test_unauthorized_access_to_visualization_endpoints(): void
    {
        $response = $this->getJson('/api/visualization/extraction-trends');
        $response->assertStatus(401);

        $response = $this->getJson('/api/visualization/comparative-analysis');
        $response->assertStatus(401);

        $response = $this->getJson('/api/visualization/optimal-parameters');
        $response->assertStatus(401);

        $response = $this->getJson('/api/visualization/summary-stats');
        $response->assertStatus(401);
    }
}