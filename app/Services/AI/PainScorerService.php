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
                "urgency" => [
                    "type" => "integer",
                    "minimum" => 1,
                    "maximum" => 10,
                    "description" => "How urgently users need this problem solved (1 = not urgent, 10 = critical)."
                ],
                "frequency" => [
                    "type" => "integer",
                    "minimum" => 1,
                    "maximum" => 10,
                    "description" => "How often users experience this problem (1 = rarely, 10 = very frequently)."
                ],
                "willingness_to_pay" => [
                    "type" => "integer",
                    "minimum" => 1,
                    "maximum" => 10,
                    "description" => "How willing users would be to pay for a solution (1 = not willing, 10 = very willing)."
                ]
            ],
            "required" => ["urgency", "frequency", "willingness_to_pay"]
        ];

        $input  = "Score for urgency, frequency and willingness to pay (1-10):\n\n" . $text;


        // $input = [
        //    ['role'=>'system','content'=>'You are a product analyst. Output strict JSON with 1-10 values.'],
        //    ['role'=>'user','content'=>"Score for urgency, frequency and willingness to pay (1-10):\n\n".$text]
        // ];

        $result = ChatGpt::generateContent($input, $schema);

        if (is_string($result)) {
            $decoded = json_decode($result, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
    }
}
