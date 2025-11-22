<?php

namespace App\Services\AI;

use ChatGpt;
use Illuminate\Support\Facades\Log;

class KeywordTrafficService
{
    public function analyze(string $problemText, array $keywords): array
    {
        $schema = [
            "type" => "object",
            "properties" => [
                "search_volume_overview" => [
                    "type" => "object",
                    "properties" => [
                        "total_monthly_searches" => ["type" => "integer"],
                        "growth_percentage" => ["type" => "integer", "description" => "Year over year growth percentage"]
                    ],
                    "required" => ["total_monthly_searches", "growth_percentage"]
                ],
                "monthly_trends" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "month" => ["type" => "string", "description" => "Month name (e.g., Jan, Feb)"],
                            "volume" => ["type" => "integer"],
                            "growth" => ["type" => "integer", "description" => "Percentage growth from previous month"]
                        ],
                        "required" => ["month", "volume", "growth"]
                    ],
                    "description" => "Trend data for the last 4 months"
                ],
                "top_keywords" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "keyword" => ["type" => "string"],
                            "volume" => ["type" => "string", "description" => "e.g., '1.2k/mo'"],
                            "cpc" => ["type" => "string", "description" => "e.g., '$2.50 CPC'"]
                        ],
                        "required" => ["keyword", "volume", "cpc"]
                    ]
                ],
                "market_opportunity" => [
                    "type" => "string",
                    "description" => "Brief analysis of market demand and competition based on keywords."
                ]
            ],
            "required" => ["search_volume_overview", "monthly_trends", "top_keywords", "market_opportunity"]
        ];

        $keywordString = implode(', ', $keywords);
        $prompt = "Analyze the search traffic potential for the following problem and keywords. Estimate realistic search volumes, trends, and CPC data as if you were an SEO tool like Ahrefs or SEMrush.
        
        Problem: {$problemText}
        Keywords: {$keywordString}
        
        Provide data for:
        1. Total monthly searches across these topics.
        2. Monthly trend for the last 4 months (Jan, Feb, Mar, Apr).
        3. Top 2 performing keywords with volume and CPC.
        4. A brief market opportunity summary.";

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
            Log::error("KeywordTrafficService error: " . $e->getMessage());
            return [
                'search_volume_overview' => ['total_monthly_searches' => 0, 'growth_percentage' => 0],
                'monthly_trends' => [],
                'top_keywords' => [],
                'market_opportunity' => 'Data unavailable'
            ];
        }
    }
}
