<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserSubmissionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $source = Source::firstOrCreate(
            ['name' => 'user_submission'],
            [
                'type' => 'user',
                'config' => ['description' => 'Manual user submissions'],
                'active' => true,
            ]
        );

        $problem = Problem::create([
            'source_id' => $source->id,
            'external_id' => 'user_' . uniqid(),
            'title' => $request->input('title'),
            'body' => $request->input('description'),
            'author' => $request->input('email') ?? 'Anonymous',
            'status' => 'raw',
            'votes' => 0,
        ]);

        return response()->json([
            'message' => 'Idea submitted successfully!',
            'id' => $problem->id
        ], 201);
    }
}
