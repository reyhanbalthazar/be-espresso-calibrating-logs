<?php

namespace App\Http\Controllers;

use App\Models\Grinder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class GrinderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $grinders = Grinder::where('coffee_shop_id', $coffeeShopId)
            ->withCount('calibrationSessions')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($grinders);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'model' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $grinder = Grinder::create(array_merge($request->all(), ['coffee_shop_id' => $coffeeShopId]));

        return response()->json($grinder, 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $grinder = Grinder::where('coffee_shop_id', $coffeeShopId)
            ->with(['calibrationSessions' => function ($query) {
                $query->with(['bean', 'user'])
                    ->orderBy('session_date', 'desc')
                    ->limit(10);
            }])->findOrFail($id);

        return response()->json($grinder);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $grinder = Grinder::where('coffee_shop_id', $coffeeShopId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:150',
            'model' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $grinder->update($request->all());

        return response()->json($grinder);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $coffeeShopId = $user->coffee_shop_id;

        $grinder = Grinder::where('coffee_shop_id', $coffeeShopId)->findOrFail($id);
        $grinder->delete();

        return response()->json(['message' => 'Grinder deleted successfully']);
    }
}
