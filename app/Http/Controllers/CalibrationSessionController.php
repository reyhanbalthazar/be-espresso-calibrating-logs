<?php

namespace App\Http\Controllers;

use App\Models\CalibrationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalibrationSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = CalibrationSession::with(['bean', 'grinder', 'user', 'shots'])
            ->orderBy('session_date', 'desc');

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bean_id' => 'required|exists:beans,id',
            'grinder_id' => 'required|exists:grinders,id',
            'session_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $session = CalibrationSession::create([
            'bean_id' => $request->bean_id,
            'grinder_id' => $request->grinder_id,
            'user_id' => auth()->id(),
            'session_date' => $request->session_date,
            'notes' => $request->notes,
        ]);

        return response()->json($session->load(['bean', 'grinder', 'user']), 201);
    }

    public function show($id)
    {
        $session = CalibrationSession::with(['bean', 'grinder', 'user', 'shots'])
            ->findOrFail($id);

        return response()->json($session);
    }

    public function update(Request $request, $id)
    {
        $session = CalibrationSession::findOrFail($id);

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
        $session = CalibrationSession::findOrFail($id);

        // Ensure user owns this session
        if ($session->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session->delete();

        return response()->json(['message' => 'Session deleted successfully']);
    }
}
