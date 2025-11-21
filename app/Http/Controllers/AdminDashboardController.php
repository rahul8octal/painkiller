<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $ideas = Idea::with('problem')->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($ideas);
    }

    public function approve($id)
    {
        $idea = Idea::findOrFail($id);
        $idea->update(['review_status' => 'approved']);
        return response()->json(['message' => 'Idea approved', 'idea' => $idea]);
    }

    public function reject($id)
    {
        $idea = Idea::findOrFail($id);
        $idea->update(['review_status' => 'rejected']);
        return response()->json(['message' => 'Idea rejected', 'idea' => $idea]);
    }

    public function update(Request $request, $id)
    {
        $idea = Idea::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'structured' => 'sometimes|string',
            'solution' => 'sometimes|string', // JSON string
            'complexity' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idea->update($request->only(['structured', 'solution', 'complexity']));

        return response()->json(['message' => 'Idea updated', 'idea' => $idea]);
    }
}
