<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CalibrationSession;
use App\Models\Bean;
use App\Models\Grinder;
use App\Models\Shot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LandingPageController extends Controller
{
    /**
     * Get personalized dashboard data for the authenticated user
     */
    public function getDashboardData()
    {
        $user = Auth::user();

        // Get user's coffee shop ID
        $coffeeShopId = $user->coffee_shop_id;

        // 1. Basic counts for the user's coffee shop
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'coffee_shop_id' => $coffeeShopId,
            ],
            'counts' => $this->getCounts($user->id, $coffeeShopId),
            'recent_sessions' => $this->getRecentSessions($user->id),
            'bean_stats' => $this->getBeanStatistics($user->id),
            'grinder_stats' => $this->getGrinderStatistics($user->id),
            'shot_performance' => $this->getShotPerformance($user->id),
            'monthly_activity' => $this->getMonthlyActivity($user->id),
            'taste_profile' => $this->getTasteProfile($user->id),
        ];

        return response()->json($data);
    }

    /**
     * Get counts for various entities
     */
    private function getCounts($userId, $coffeeShopId)
    {
        return [
            'beans' => Bean::where('coffee_shop_id', $coffeeShopId)->count(),
            'grinders' => Grinder::where('coffee_shop_id', $coffeeShopId)->count(),
            'sessions' => CalibrationSession::where('user_id', $userId)->count(),
            'shots' => Shot::whereHas('calibrationSession', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count(),
        ];
    }

    /**
     * Get recent calibration sessions
     */
    private function getRecentSessions($userId)
    {
        return CalibrationSession::with(['bean', 'grinder'])
            ->where('user_id', $userId)
            ->orderBy('session_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'date' => $session->session_date,
                    'bean_name' => $session->bean->name,
                    'grinder_name' => $session->grinder->name,
                    'notes' => $session->notes,
                    'shots_count' => $session->shots()->count(),
                ];
            });
    }

    /**
     * Get bean usage statistics
     */
    private function getBeanStatistics($userId)
    {
        return CalibrationSession::with('bean')
            ->where('user_id', $userId)
            ->selectRaw('bean_id, COUNT(*) as session_count')
            ->groupBy('bean_id')
            ->orderBy('session_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'bean_id' => $item->bean_id,
                    'bean_name' => $item->bean->name,
                    'origin' => $item->bean->origin,
                    'roast_level' => $item->bean->roast_level,
                    'session_count' => $item->session_count,
                ];
            });
    }

    /**
     * Get grinder usage statistics
     */
    private function getGrinderStatistics($userId)
    {
        return CalibrationSession::with('grinder')
            ->where('user_id', $userId)
            ->selectRaw('grinder_id, COUNT(*) as usage_count')
            ->groupBy('grinder_id')
            ->orderBy('usage_count', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'grinder_id' => $item->grinder_id,
                    'grinder_name' => $item->grinder->name,
                    'model' => $item->grinder->model,
                    'usage_count' => $item->usage_count,
                ];
            });
    }

    /**
     * Get shot performance metrics
     */
    private function getShotPerformance($userId)
    {
        $shots = Shot::whereHas('calibrationSession', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->selectRaw('
                AVG(dose) as avg_dose,
                AVG(yield) as avg_yield,
                AVG(time_seconds) as avg_time,
                AVG(water_temperature) as avg_temp,
                COUNT(*) as total_shots
            ')
            ->first();

        // Get last session's shots for comparison
        $lastSession = CalibrationSession::where('user_id', $userId)
            ->orderBy('session_date', 'desc')
            ->first();

        $lastSessionShots = [];
        if ($lastSession) {
            $lastSessionShots = $lastSession->shots()
                ->orderBy('shot_number', 'asc')
                ->limit(3)
                ->get()
                ->map(function ($shot) {
                    return [
                        'shot_number' => $shot->shot_number,
                        'dose' => $shot->dose,
                        'yield' => $shot->yield,
                        'time' => $shot->time_seconds,
                        'temp' => $shot->water_temperature,
                        'taste_notes' => $shot->taste_notes,
                    ];
                });
        }

        return [
            'averages' => [
                'dose' => round($shots->avg_dose ?? 0, 2),
                'yield' => round($shots->avg_yield ?? 0, 2),
                'time' => round($shots->avg_time ?? 0, 1),
                'temperature' => round($shots->avg_temp ?? 0, 1),
            ],
            'last_session_shots' => $lastSessionShots,
        ];
    }

    /**
     * Get monthly activity for charts
     */
    private function getMonthlyActivity($userId)
    {
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        $sessionsByMonth = CalibrationSession::where('user_id', $userId)
            ->where('session_date', '>=', $sixMonthsAgo)
            ->selectRaw('
                DATE_FORMAT(session_date, "%Y-%m") as month,
                COUNT(*) as session_count
            ')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('session_count', 'month');

        $shotsByMonth = Shot::whereHas('calibrationSession', function ($query) use ($userId, $sixMonthsAgo) {
            $query->where('user_id', $userId)
                ->where('session_date', '>=', $sixMonthsAgo);
        })
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as shot_count
            ')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('shot_count', 'month');

        return [
            'labels' => $sessionsByMonth->keys()->toArray(),
            'sessions' => $sessionsByMonth->values()->toArray(),
            'shots' => $shotsByMonth->values()->toArray(),
        ];
    }

    /**
     * Extract taste profile from shot notes
     */
    private function getTasteProfile($userId)
    {
        $shotsWithNotes = Shot::whereHas('calibrationSession', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereNotNull('taste_notes')
            ->select('taste_notes')
            ->get()
            ->pluck('taste_notes');

        if ($shotsWithNotes->isEmpty()) {
            return [
                'common_flavors' => [],
                'flavor_categories' => [],
                'total_taste_notes_logged' => 0,
                'most_common_words' => []
            ];
        }

        // Combine all notes into one string
        $allNotes = $shotsWithNotes->implode(' ');
        $allNotes = strtolower($allNotes);

        // Define flavor categories
        $flavorCategories = [
            'sweet' => ['sweet', 'honey', 'caramel', 'chocolate', 'sugar'],
            'fruity' => ['berry', 'fruit', 'citrus', 'lemon', 'apple', 'cherry'],
            'floral' => ['floral', 'jasmine', 'bergamot', 'tea'],
            'nutty' => ['nutty', 'almond', 'hazelnut', 'peanut'],
            'earthy' => ['earthy', 'herbal', 'spice', 'woody', 'tobacco'],
            'acidic' => ['bright', 'acid', 'tangy', 'sharp', 'vibrant'],
            'bitter' => ['bitter', 'harsh', 'astringent'],
            'balanced' => ['balanced', 'smooth', 'clean', 'well-rounded'],
            'complex' => ['complex', 'layered', 'depth', 'nuanced']
        ];

        // Analyze for each category
        $categoryCounts = [];
        foreach ($flavorCategories as $category => $keywords) {
            $count = 0;
            foreach ($keywords as $keyword) {
                $count += substr_count($allNotes, $keyword);
            }
            if ($count > 0) {
                $categoryCounts[$category] = $count;
            }
        }

        // Sort by frequency
        arsort($categoryCounts);

        // Get top 5 individual flavor words (excluding common words)
        $commonWords = ['the', 'and', 'with', 'like', 'notes', 'note', 'finish', 'aftertaste'];
        $words = str_word_count($allNotes, 1);
        $wordCounts = array_count_values($words);

        // Remove common words
        foreach ($commonWords as $common) {
            unset($wordCounts[$common]);
        }

        arsort($wordCounts);
        $topWords = array_slice($wordCounts, 0, 10);

        return [
            'common_flavors' => array_slice(array_keys($categoryCounts), 0, 5),
            'flavor_categories' => $categoryCounts,
            'total_taste_notes_logged' => $shotsWithNotes->count(),
            'most_common_words' => $topWords
        ];
    }
}
