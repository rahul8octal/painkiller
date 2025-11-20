<?php

namespace App\Services\AI;

use ChatGpt;

class GoToMarketMatcherService
{
    public function match(array $payload)
    {
        // $schema = [ /* json_schema: plays: [{title, type, short_reason}] */ ];

        $schema = [
            "type" => "object",
            "properties" => [
                "plays" => [
                    "type" => "array",
                    "description" => "A list of recommended Go-to-Market plays based on the provided problem or signals.",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "title" => [
                                "type" => "string",
                                "description" => "The concise name of the GTM play (e.g., 'Freemium Launch Strategy', 'Influencer Partnership')."
                            ],
                            "type" => [
                                "type" => "string",
                                "description" => "Category or type of the play (e.g., Growth, Sales, Marketing, Product, Community, PR)."
                            ],
                            "short_reason" => [
                                "type" => "string",
                                "description" => "A brief explanation of why this play fits the problem context."
                            ]
                        ],
                        "required" => ["title", "type", "short_reason"]
                    ]
                ]
            ],
            "required" => ["plays"]
        ];


        // $input = [
        //    ['role'=>'system','content'=>'You are a GTM strategist. Output strict JSON.'],
        //    ['role'=>'user','content'=>"Given the following problem metadata and signals, suggest 2-3 GTM plays:\n\n".json_encode($payload)]
        // ];

        $input = "Given the following problem metadata and signals, suggest 2-3 GTM plays:\n\n" . json_encode($payload);

        $result = ChatGpt::generateContent($input, $schema);

        if (is_string($result)) {
            $decoded = json_decode($result, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
    }
}
