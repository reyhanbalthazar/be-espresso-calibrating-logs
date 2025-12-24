<?php

namespace App\Http\Controllers;

use App\Models\CalibrationSession;
use App\Models\Shot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataVisualizationController extends Controller
{
    /**
     * Get extraction parameter trends for a specific bean or all beans
     */
    public function extractionTrends(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $query = Shot::whereHas('calibrationSession', function($q) use ($coffeeShopId) {
            $q->where('coffee_shop_id', $coffeeShopId);
        })
        ->with(['calibrationSession:id,session_date,bean_id', 'calibrationSession.bean:id,name'])
        ->orderBy('created_at', 'asc');

        if ($request->has('bean_id')) {
            $query->whereHas('calibrationSession', function($q) use ($request) {
                $q->where('bean_id', $request->bean_id);
            });
        }

        if ($request->has('session_id')) {
            $query->where('calibration_session_id', $request->session_id);
        }

        $shots = $query->get();

        // Prepare data for extraction trends
        $extractionData = $shots->map(function ($shot) {
            return [
                'id' => $shot->id,
                'session_date' => $shot->calibrationSession->session_date->format('Y-m-d'),
                'session_id' => $shot->calibration_session_id,
                'bean_name' => $shot->calibrationSession->bean->name,
                'shot_number' => $shot->shot_number,
                'extraction_yield' => round($shot->extraction_yield, 2),
                'extraction_ratio' => round($shot->extraction_ratio, 2),
                'flow_rate' => round($shot->flow_rate, 2),
                'dose' => $shot->dose,
                'yield' => $shot->yield,
                'time_seconds' => $shot->time_seconds,
                'grind_setting' => $shot->grind_setting,
            ];
        });

        return response()->json([
            'data' => $extractionData,
            'summary' => [
                'avg_extraction_yield' => round($extractionData->avg('extraction_yield'), 2),
                'avg_extraction_ratio' => round($extractionData->avg('extraction_ratio'), 2),
                'avg_flow_rate' => round($extractionData->avg('flow_rate'), 2),
                'total_shots' => $extractionData->count(),
            ]
        ]);
    }

    /**
     * Get comparative analysis between sessions
     */
    public function comparativeAnalysis(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $sessionIds = $request->input('session_ids', []);
        
        $query = CalibrationSession::where('coffee_shop_id', $coffeeShopId)
            ->with(['shots', 'bean:id,name', 'grinder:id,name,model'])
            ->orderBy('session_date', 'desc');

        if (!empty($sessionIds)) {
            $query->whereIn('id', $sessionIds);
        }

        // Limit to recent sessions if no specific IDs provided
        if (empty($sessionIds)) {
            $query->limit(10); // Last 10 sessions
        }

        $sessions = $query->get();

        $comparativeData = $sessions->map(function ($session) {
            if ($session->shots->count() === 0) {
                return null;
            }

            $shots = $session->shots;
            
            return [
                'session_id' => $session->id,
                'session_date' => $session->session_date->format('Y-m-d'),
                'bean_name' => $session->bean->name,
                'grinder_model' => $session->grinder->name . ' ' . $session->grinder->model,
                'shot_count' => $shots->count(),
                'avg_extraction_yield' => round($shots->avg('extraction_yield'), 2),
                'avg_extraction_ratio' => round($shots->avg('extraction_ratio'), 2),
                'avg_flow_rate' => round($shots->avg('flow_rate'), 2),
                'avg_dose' => round($shots->avg('dose'), 2),
                'avg_yield' => round($shots->avg('yield'), 2),
                'avg_time_seconds' => round($shots->avg('time_seconds'), 2),
                'avg_grind_setting' => round($shots->avg('grind_setting'), 2),
                'first_shot' => [
                    'dose' => $shots->first()->dose,
                    'yield' => $shots->first()->yield,
                    'time_seconds' => $shots->first()->time_seconds,
                    'extraction_yield' => round($shots->first()->extraction_yield, 2),
                ],
                'last_shot' => [
                    'dose' => $shots->last()->dose,
                    'yield' => $shots->last()->yield,
                    'time_seconds' => $shots->last()->time_seconds,
                    'extraction_yield' => round($shots->last()->extraction_yield, 2),
                ],
            ];
        })->filter(); // Remove null values

        return response()->json([
            'data' => $comparativeData,
            'summary' => [
                'total_sessions' => $comparativeData->count(),
                'avg_session_shots' => round($comparativeData->avg('shot_count') ?? 0, 2),
                'overall_avg_extraction_yield' => round($comparativeData->avg('avg_extraction_yield') ?? 0, 2),
                'overall_avg_extraction_ratio' => round($comparativeData->avg('avg_extraction_ratio') ?? 0, 2),
            ]
        ]);
    }

    /**
     * Get optimal parameter recommendations
     */
    public function optimalParameters(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        // Get all shots for the coffee shop
        $shots = Shot::whereHas('calibrationSession', function($q) use ($coffeeShopId) {
            $q->where('coffee_shop_id', $coffeeShopId);
        })
        ->with(['calibrationSession.bean:id,name'])
        ->get();

        if ($shots->isEmpty()) {
            return response()->json([
                'optimal_parameters' => null,
                'recommendations' => [],
                'message' => 'No data available for analysis'
            ]);
        }

        // Calculate optimal parameters based on best extraction yields
        // Generally, ideal extraction yield is between 18-22%
        $optimalShots = $shots->filter(function ($shot) {
            return $shot->extraction_yield >= 18 && $shot->extraction_yield <= 22;
        });

        $optimalParameters = null;
        $recommendations = [];

        if ($optimalShots->isNotEmpty()) {
            $optimalParameters = [
                'avg_grind_setting' => round($optimalShots->avg('grind_setting'), 2),
                'avg_dose' => round($optimalShots->avg('dose'), 2),
                'avg_yield' => round($optimalShots->avg('yield'), 2),
                'avg_time_seconds' => round($optimalShots->avg('time_seconds'), 2),
                'avg_extraction_yield' => round($optimalShots->avg('extraction_yield'), 2),
                'avg_extraction_ratio' => round($optimalShots->avg('extraction_ratio'), 2),
                'avg_flow_rate' => round($optimalShots->avg('flow_rate'), 2),
            ];
        }

        // Generate recommendations based on data
        $allExtractionYields = $shots->pluck('extraction_yield')->filter();
        $avgExtractionYield = $allExtractionYields->avg();
        
        if ($avgExtractionYield < 18) {
            $recommendations[] = [
                'type' => 'under_extraction',
                'message' => 'Average extraction yield is below ideal range (18-22%). Consider grinding finer or increasing extraction time.',
                'severity' => 'medium'
            ];
        } elseif ($avgExtractionYield > 22) {
            $recommendations[] = [
                'type' => 'over_extraction',
                'message' => 'Average extraction yield is above ideal range (18-22%). Consider grinding coarser or decreasing extraction time.',
                'severity' => 'medium'
            ];
        } else {
            $recommendations[] = [
                'type' => 'optimal_extraction',
                'message' => 'Average extraction yield is within ideal range (18-22%).',
                'severity' => 'low'
            ];
        }

        // Check extraction ratio (typically 1:2 to 1:3 for espresso)
        $avgExtractionRatio = $shots->avg('extraction_ratio');
        if ($avgExtractionRatio < 1.8) {
            $recommendations[] = [
                'type' => 'low_ratio',
                'message' => 'Average extraction ratio is low (target 1:1.8-1:2.2). Consider adjusting dose/yield ratio.',
                'severity' => 'medium'
            ];
        } elseif ($avgExtractionRatio > 2.5) {
            $recommendations[] = [
                'type' => 'high_ratio',
                'message' => 'Average extraction ratio is high (target 1:1.8-1:2.2). Consider adjusting dose/yield ratio.',
                'severity' => 'medium'
            ];
        }

        // Check flow rate (typically 1-4 g/sec for espresso)
        $avgFlowRate = $shots->avg('flow_rate');
        if ($avgFlowRate < 1) {
            $recommendations[] = [
                'type' => 'slow_flow',
                'message' => 'Average flow rate is slow (target 1-4 g/sec). Consider grinding coarser or checking puck preparation.',
                'severity' => 'medium'
            ];
        } elseif ($avgFlowRate > 4) {
            $recommendations[] = [
                'type' => 'fast_flow',
                'message' => 'Average flow rate is fast (target 1-4 g/sec). Consider grinding finer or improving puck preparation.',
                'severity' => 'medium'
            ];
        }

        return response()->json([
            'optimal_parameters' => $optimalParameters,
            'recommendations' => $recommendations,
            'summary' => [
                'total_shots' => $shots->count(),
                'optimal_shots_count' => $optimalShots->count(),
                'optimal_shots_percentage' => round(($optimalShots->count() / $shots->count()) * 100, 2),
                'avg_extraction_yield' => round($avgExtractionYield, 2),
                'avg_extraction_ratio' => round($avgExtractionRatio, 2),
                'avg_flow_rate' => round($avgFlowRate, 2),
            ]
        ]);
    }

    /**
     * Get summary statistics for dashboard
     */
    public function summaryStats(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $shots = Shot::whereHas('calibrationSession', function($q) use ($coffeeShopId) {
            $q->where('coffee_shop_id', $coffeeShopId);
        })->get();

        $totalShots = $shots->count();
        $optimalShots = $shots->filter(function ($shot) {
            return $shot->extraction_yield >= 18 && $shot->extraction_yield <= 22;
        })->count();

        $avgExtractionYield = $shots->avg('extraction_yield');
        $avgExtractionRatio = $shots->avg('extraction_ratio');
        $avgFlowRate = $shots->avg('flow_rate');

        return response()->json([
            'total_shots' => $totalShots,
            'optimal_shots' => $optimalShots,
            'optimal_percentage' => $totalShots > 0 ? round(($optimalShots / $totalShots) * 100, 2) : 0,
            'avg_extraction_yield' => round($avgExtractionYield ?? 0, 2),
            'avg_extraction_ratio' => round($avgExtractionRatio ?? 0, 2),
            'avg_flow_rate' => round($avgFlowRate ?? 0, 2),
        ]);
    }
}