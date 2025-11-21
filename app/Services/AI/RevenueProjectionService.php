<?php

namespace App\Services\AI;

use ChatGpt;
use Illuminate\Support\Facades\Log;

class RevenueProjectionService
{
    public function predict(string $problemText, array $scores): array
    {
        $schema = [
            "type" => "object",
            "properties" => [
                "min_revenue" => [
                    "type" => "number",
                    "description" => "Minimum estimated annual revenue in USD."
                ],
                "max_revenue" => [
                    "type" => "number",
                    "description" => "Maximum estimated annual revenue in USD."
                ],
                "currency" => [
                    "type" => "string",
                    "enum" => ["USD"],
                    "description" => "Currency code."
                ],
                "assumptions" => [
                    "type" => "object",
                    "properties" => [
                        "target_audience" => ["type" => "string"],
                        "base_user_count" => ["type" => "integer"],
                        "price_per_user" => ["type" => "number"]
                    ],
                    "required" => ["target_audience", "base_user_count", "price_per_user"]
                ]
            ],
            "required" => ["min_revenue", "max_revenue", "currency", "assumptions"]
        ];

        $prompt = "Analyze the following problem and business fit scores. Estimate a realistic Annual Revenue Range for a micro-SaaS or startup solving this.
        
        Problem: {$problemText}
        Scores: " . json_encode($scores) . "
        
        Provide conservative estimates based on similar niche tools.";

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
            Log::error("RevenueProjectionService error: " . $e->getMessage());
            return [
                'min_revenue' => 0,
                'max_revenue' => 0,
                'currency' => 'USD',
                'assumptions' => []
            ];
        }
    }
}
