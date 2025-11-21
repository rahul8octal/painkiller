<?php

namespace App\Services\AI;

use ChatGpt;

class PainScorerService
{
    public function score(string $text)
    {
        // $schema = [ /* json_schema: urgency int, frequency int, willingness_to_pay int */ ];

        $schema = [
            "type" => "object",
            "properties" => [
                "opportunity" => [
                    "type" => "integer",
                    "minimum" => 0,
                    "maximum" => 100,
                    "description" => "Market opportunity size and potential (0-100)."
                ],
                "pain" => [
                    "type" => "integer",
                    "minimum" => 0,
                    "maximum" => 100,
                    "description" => "Severity of pain being solved (0-100)."
                ],
                "feasibility" => [
                    "type" => "integer",
                    "minimum" => 0,
                    "maximum" => 100,
                    "description" => "Technical and resource feasibility (0-100)."
                ],
                "why_now" => [
                    "type" => "integer",
                    "minimum" => 0,
                    "maximum" => 100,
                    "description" => "Market timing and urgency (0-100)."
                ],
                "revenue_potential" => [
                    "type" => "integer",
                    "minimum" => 0,
                    "maximum" => 100,
                    "description" => "Monetization opportunity (0-100)."
                ],
                "execution_difficulty" => [
                    "type" => "integer",
                    "minimum" => 0,
                    "maximum" => 100,
                    "description" => "Complexity of execution (0 = Very Hard, 100 = Very Easy/Low Difficulty). Note: Higher score means BETTER for business."
                ],
                "go_to_market" => [
                    "type" => "integer",
                    "minimum" => 0,
                    "maximum" => 100,
                    "description" => "Market accessibility and reach (0-100)."
                ]
            ],
            "required" => ["opportunity", "pain", "feasibility", "why_now", "revenue_potential", "execution_difficulty", "go_to_market"]
        ];

        $input  = "Score the following problem statement based on Business Fit (0-100 scale):\n\n" . $text;


        // $input = [
        //    ['role'=>'system','content'=>'You are a product analyst. Output strict JSON with 1-10 values.'],
        //    ['role'=>'user','content'=>"Score for urgency, frequency and willingness to pay (1-10):\n\n".$text]
        // ];

        try {
            $result = ChatGpt::generateContent($input, $schema);

            if (is_string($result)) {
                $decoded = json_decode($result, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
            // If result is already an array
            if (is_array($result)) {
                return $result;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("PainScorerService error: " . $e->getMessage());
        }

        return [];
    }
}
