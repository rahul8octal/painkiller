<?php

namespace App\Services\AI;
use ChatGpt;


class FatchProblemService {
    public function problems(string $text) {

     $schema = [
        "type" => "object",
        "properties" => [
            "summary" => [
                "type" => "string",
                "description" => "A concise one-sentence summary describing the main problem or pain point."
            ],
            "industry" => [
                "type" => "string",
                "description" => "The business or industry domain most related to the problem (e.g., healthcare, finance, SaaS)."
            ],
            "keywords" => [
                "type" => "array",
                "items" => [
                    "type" => "string",
                    "description" => "A keyword or phrase that represents a main concept from the problem statement."
                ],
                "description" => "A list of key concepts, entities, or recurring terms found in the text."
            ],
            "sentiment" => [
                "type" => "string",
                "enum" => ["positive", "negative", "neutral"],
                "description" => "The overall tone or emotional sentiment expressed in the problem statement."
            ]
        ],
        "required" => ["summary", "industry", "keywords", "sentiment"]
    ];

        $input = "Extract summary, industry, keywords, sentiment from the text:\n\n".$text;

        // $input = [
        //    ['role'=>'system','content'=>'You are an extractor. Output strict JSON.'],
        //    ['role'=>'user','content'=>"Extract summary, industry, keywords, sentiment from the text:\n\n".$text]
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
