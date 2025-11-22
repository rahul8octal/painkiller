<?php

namespace App\Services\AI;

use ChatGpt;
use Illuminate\Support\Facades\Log;

class CommunitySignalsService
{
    public function analyze(string $problemText, array $keywords): array
    {
        $schema = [
            "type" => "object",
            "properties" => [
                "platform_breakdown" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "platform" => ["type" => "string", "description" => "e.g., Reddit, Twitter, IndieHackers"],
                            "discussion_volume" => ["type" => "string", "description" => "High, Medium, Low"],
                            "relevance" => ["type" => "integer", "description" => "Score 0-100"]
                        ],
                        "required" => ["platform", "discussion_volume", "relevance"]
                    ]
                ],
                "sentiment_analysis" => [
                    "type" => "object",
                    "properties" => [
                        "positive" => ["type" => "integer", "description" => "Percentage 0-100"],
                        "neutral" => ["type" => "integer", "description" => "Percentage 0-100"],
                        "negative" => ["type" => "integer", "description" => "Percentage 0-100"],
                        "summary" => ["type" => "string", "description" => "Brief summary of community sentiment"]
                    ],
                    "required" => ["positive", "neutral", "negative", "summary"]
                ],
                "key_discussions" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "platform" => ["type" => "string"],
                            "title" => ["type" => "string"],
                            "url" => ["type" => "string"],
                            "summary" => ["type" => "string"]
                        ],
                        "required" => ["platform", "title", "url", "summary"]
                    ]
                ],
                "common_pain_points" => [
                    "type" => "array",
                    "items" => ["type" => "string"]
                ],
                "feature_requests" => [
                    "type" => "array",
                    "items" => ["type" => "string"]
                ]
            ],
            "required" => ["platform_breakdown", "sentiment_analysis", "key_discussions", "common_pain_points", "feature_requests"]
        ];

        $keywordString = implode(', ', $keywords);
        $prompt = "Analyze the community signals for the following problem and keywords. Simulate a search across platforms like Reddit, Twitter, Product Hunt, and Indie Hackers.
        
        Problem: {$problemText}
        Keywords: {$keywordString}
        
        Provide:
        1. A breakdown of discussion volume by platform.
        2. Sentiment analysis of the discussions.
        3. Key discussions (simulate titles and summaries).
        4. Common pain points mentioned by users.
        5. Frequent feature requests.";

        try {
            $result = ChatGpt::generateContent($prompt, $schema);

            if (is_string($result)) {
                $decoded = json_decode($result, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
            return $result;
        } catch (\Exception $e) {
            Log::error("CommunitySignalsService error: " . $e->getMessage());
            return [
                'platform_breakdown' => [],
                'sentiment_analysis' => ['positive' => 0, 'neutral' => 0, 'negative' => 0, 'summary' => 'No data'],
                'key_discussions' => [],
                'common_pain_points' => [],
                'feature_requests' => []
            ];
        }
    }
}
