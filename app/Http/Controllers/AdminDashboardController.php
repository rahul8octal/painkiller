<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Problem;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Idea::with('problem')->orderBy('created_at', 'desc');

        // if ($request->has('status') && $request->status !== 'all') {
        //     $query->where('review_status', $request->status);
        // }

        $ideas = $query->paginate(20);
        return response()->json($ideas);
    }

    public function show($id)
    {
        $idea = Idea::with('problem')->findOrFail($id);
        return response()->json($idea);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a dummy problem for the manual idea
        $source = Source::firstOrCreate(['name' => 'admin_manual']);

        $problem = Problem::create([
            'source_id' => $source->id,
            'external_id' => 'admin_' . uniqid(),
            'title' => $request->title,
            'body' => $request->description,
            'status' => 'processed',
            'author' => 'Admin',
            'votes' => 0,
        ]);

        $idea = Idea::create([
            'problem_id' => $problem->id,
            'review_status' => $request->status,
            'revenue_potential' => $request->revenue_potential ?? [],
            'market_validation' => $request->market_validation ?? [],
            'creative_assets' => $request->creative_assets ?? [],
        ]);

        return response()->json(['message' => 'Idea created successfully', 'idea' => $idea]);
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
        $idea = Idea::with('problem')->findOrFail($id);

        $idea->update($request->only(['review_status', 'revenue_potential', 'market_validation', 'creative_assets']));

        if ($request->has('title') || $request->has('description')) {
            $idea->problem->update([
                'title' => $request->title ?? $idea->problem->title,
                'body' => $request->description ?? $idea->problem->body,
            ]);
        }

        return response()->json(['message' => 'Idea updated', 'idea' => $idea]);
    }

    public function destroy($id)
    {
        $idea = Idea::findOrFail($id);
        $idea->problem()->delete(); // Cascade delete problem if needed, or just the idea
        $idea->delete();
        return response()->json(['message' => 'Idea deleted successfully']);
    }
}