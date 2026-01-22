<?php

namespace App\Http\Controllers;

use App\Models\CalibrationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CalibrationSessionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $query = CalibrationSession::where('coffee_shop_id', $coffeeShopId)
            ->with(['bean', 'grinder', 'user', 'shots'])
            ->orderBy('created_at', 'desc');

        if ($request->has('bean_id')) {
            $query->where('bean_id', $request->bean_id);
        }

        if ($request->has('grinder_id')) {
            $query->where('grinder_id', $request->grinder_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $sessions = $query->paginate(20);

        return response()->json($sessions);
    }

    public function sessionsByBean(Request $request, $bean)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $query = CalibrationSession::where('coffee_shop_id', $coffeeShopId)
            ->where('bean_id', $bean)
            ->with(['bean', 'grinder', 'user', 'shots'])
            ->orderBy('created_at', 'desc');

        $sessions = $query->paginate(20);

        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $validator = Validator::make($request->all(), [
            'bean_id' => 'required|exists:beans,id',
            'grinder_id' => 'required|exists:grinders,id',
            'session_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify that the bean and grinder belong to the same coffee shop
        $bean = \App\Models\Bean::where('id', $request->bean_id)
            ->where('coffee_shop_id', $coffeeShopId)
            ->firstOrFail();

        $grinder = \App\Models\Grinder::where('id', $request->grinder_id)
            ->where('coffee_shop_id', $coffeeShopId)
            ->firstOrFail();

        $session = CalibrationSession::create([
            'bean_id' => $request->bean_id,
            'grinder_id' => $request->grinder_id,
            'user_id' => auth()->id(),
            'coffee_shop_id' => $coffeeShopId,
            'session_date' => $request->session_date,
            'notes' => $request->notes,
        ]);

        return response()->json($session->load(['bean', 'grinder', 'user']), 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $session = CalibrationSession::where('coffee_shop_id', $coffeeShopId)
            ->with(['bean', 'grinder', 'user', 'shots'])
            ->findOrFail($id);

        return response()->json($session);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $session = CalibrationSession::where('coffee_shop_id', $coffeeShopId)->findOrFail($id);

        // Ensure user owns this session
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'session_date' => 'sometimes|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $session->update($request->only(['session_date', 'notes']));

        return response()->json($session->load(['bean', 'grinder', 'user', 'shots']));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $session = CalibrationSession::where('coffee_shop_id', $coffeeShopId)->findOrFail($id);

        // Ensure user owns this session
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session->delete();

        return response()->json(['message' => 'Session deleted successfully']);
    }
}
