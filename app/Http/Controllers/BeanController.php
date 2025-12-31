<?php

namespace App\Http\Controllers;

use App\Models\Bean;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BeanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $beans = Bean::where('coffee_shop_id', $coffeeShopId)
            ->withCount('calibrationSessions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($bean) {
                return [
                    'id' => $bean->id,
                    'name' => $bean->name,
                    'origin' => $bean->origin,
                    'roastery' => $bean->roastery,
                    'roast_level' => $bean->roast_level,
                    'roast_date' => $bean->roast_date,
                    'roast_age' => $bean->roast_age,
                    'is_blend' => $bean->is_blend,
                    'notes' => $bean->notes,
                    'sessions_count' => $bean->calibration_sessions_count,
                    'created_at' => $bean->created_at,
                ];
            });

        return response()->json($beans);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'origin' => 'nullable|string|max:150',
            'roastery' => 'nullable|string|max:150',
            'roast_level' => 'nullable|in:light,medium,dark',
            'roast_date' => 'nullable|date',
            'is_blend' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bean = Bean::create(array_merge($request->all(), ['coffee_shop_id' => $coffeeShopId]));

        return response()->json($bean, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $bean = Bean::where('coffee_shop_id', $coffeeShopId)
            ->with(['calibrationSessions' => function ($query) {
                $query->with(['grinder', 'user'])
                    ->orderBy('session_date', 'desc')
                    ->limit(10);
            }])->findOrFail($id);

        return response()->json([
            'bean' => $bean,
            'recent_sessions' => $bean->calibrationSessions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $bean = Bean::where('coffee_shop_id', $coffeeShopId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:150',
            'origin' => 'nullable|string|max:150',
            'roastery' => 'nullable|string|max:150',
            'roast_level' => 'nullable|in:light,medium,dark',
            'roast_date' => 'nullable|date',
            'is_blend' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bean->update($request->all());

        return response()->json($bean);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $bean = Bean::where('coffee_shop_id', $coffeeShopId)->findOrFail($id);
        $bean->delete();

        return response()->json(['message' => 'Bean deleted successfully']);
    }
}
