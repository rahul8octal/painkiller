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

    public function show($id)
    {
        $idea = Idea::with('problem')->findOrFail($id);
        return response()->json($idea);
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


// [{"title":"Co-hosted Weekly Thread + Mod Toolkit","type":"Community","short_reason":"Partner with subreddit mods to co-brand the weekly open thread and supply moderation templates, prompts, and anti-spam macros\u2014adding real value while aligning with community guidelines and reducing non-Q&A noise."},{"title":"Operator AMA Roadshow","type":"PR","short_reason":"Run a rotating AMA series with vetted small business operators and educators; it fits the community\u2019s appetite for experiences and lessons learned, drives high-quality engagement, and respects no-spam policies."},{"title":"Weekly Thread Digest + SEO Hub","type":"Product","short_reason":"Offer a free tool that summarizes each week\u2019s thread into a digest and hosts an SEO-optimized archive; captures rising search demand, delivers educational materials, and earns permissioned opt-ins without overt promotion."}]