<?php

namespace App\Http\Controllers;

use App\Models\Grinder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GrinderController extends Controller
{
    public function index()
    {
        $grinders = Grinder::withCount('calibrationSessions')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($grinders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'model' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $grinder = Grinder::create($request->all());

        return response()->json($grinder, 201);
    }

    public function show($id)
    {
        $grinder = Grinder::with(['calibrationSessions' => function ($query) {
            $query->with(['bean', 'user'])
                ->orderBy('session_date', 'desc')
                ->limit(10);
        }])->findOrFail($id);

        return response()->json($grinder);
    }

    public function update(Request $request, $id)
    {
        $grinder = Grinder::findOrFail($id);

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
        $grinder = Grinder::findOrFail($id);
        $grinder->delete();

        return response()->json(['message' => 'Grinder deleted successfully']);
    }
}
