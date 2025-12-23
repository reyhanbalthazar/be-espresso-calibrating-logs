<?php

namespace App\Http\Controllers;

use App\Models\Shot;
use App\Models\CalibrationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
// use log
use Illuminate\Support\Facades\Log;

class ShotController extends Controller
{
    public function index($sessionId)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        // Verify the session belongs to the user's coffee shop
        $session = CalibrationSession::where('id', $sessionId)
            ->where('coffee_shop_id', $coffeeShopId)
            ->firstOrFail();

        $shots = Shot::where('calibration_session_id', $sessionId)
            ->orderBy('shot_number')
            ->get();

        return response()->json($shots);
    }

    public function store(Request $request, $sessionId)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        // Verify the session belongs to the user's coffee shop
        $session = CalibrationSession::where('id', $sessionId)
            ->where('coffee_shop_id', $coffeeShopId)
            ->firstOrFail();

        // Ensure user owns this session
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'shot_number' => 'required|integer|min:1',
            'grind_setting' => 'required|string|max:50',
            'dose' => 'required|numeric|min:0|max:999.99',
            'yield' => 'required|numeric|min:0|max:999.99',
            'time_seconds' => 'required|integer|min:1|max:999',
            'taste_notes' => 'nullable|string',
            'action_taken' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shot = Shot::create([
            'calibration_session_id' => $sessionId,
            'shot_number' => $request->shot_number,
            'grind_setting' => $request->grind_setting,
            'dose' => $request->dose,
            'yield' => $request->yield,
            'time_seconds' => $request->time_seconds,
            'taste_notes' => $request->taste_notes,
            'action_taken' => $request->action_taken,
        ]);

        return response()->json($shot, 201);
    }

    public function update(Request $request, $sessionId, $shotId)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        // Verify the session belongs to the user's coffee shop
        $session = CalibrationSession::where('id', $sessionId)
            ->where('coffee_shop_id', $coffeeShopId)
            ->firstOrFail();

        $shot = Shot::where('calibration_session_id', $sessionId)
            ->where('shot_number', $shotId)
            ->firstOrFail();

        // Ensure user owns this session
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'grind_setting' => 'sometimes|string|max:50',
            'dose' => 'sometimes|numeric|min:0|max:999.99',
            'yield' => 'sometimes|numeric|min:0|max:999.99',
            'time_seconds' => 'sometimes|integer|min:1|max:999',
            'taste_notes' => 'nullable|string',
            'action_taken' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shot->update($request->all());

        return response()->json($shot);
    }

    public function show($sessionId, $shotId)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        // Verify the session belongs to the user's coffee shop
        $session = CalibrationSession::where('id', $sessionId)
            ->where('coffee_shop_id', $coffeeShopId)
            ->firstOrFail();

        $shot = Shot::where('calibration_session_id', $sessionId)
            ->where('shot_number', $shotId)
            ->firstOrFail();

        // Ensure user owns this session
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($shot);
    }

    public function destroy($sessionId, $shotId)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        // Verify the session belongs to the user's coffee shop
        $session = CalibrationSession::where('id', $sessionId)
            ->where('coffee_shop_id', $coffeeShopId)
            ->firstOrFail();

        $shot = Shot::where('calibration_session_id', $sessionId)
            ->where('shot_number', $shotId)
            ->firstOrFail();

        // Ensure user owns this session
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $shot->delete();

        // Reorder remaining shots
        $shots = Shot::where('calibration_session_id', $sessionId)
            ->orderBy('shot_number')
            ->get();

        foreach ($shots as $index => $shot) {
            $shot->update(['shot_number' => $index + 1]);
        }

        return response()->json(['message' => 'Shot deleted successfully']);
    }
}
