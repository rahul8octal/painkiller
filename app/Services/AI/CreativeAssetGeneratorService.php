<?php

namespace App\Services\AI;

use ChatGpt;
use Illuminate\Support\Facades\Log;

class CreativeAssetGeneratorService
{
    public function generate(string $problemText, string $solutionText): array
    {
        $schema = [
            "type" => "object",
            "properties" => [
                "ad_creatives" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "platform" => ["type" => "string", "enum" => ["Facebook", "LinkedIn", "Google Ads"]],
                            "headline" => ["type" => "string"],
                            "body" => ["type" => "string", "description" => "Main ad text or description."],
                            "cta" => ["type" => "string", "description" => "Call to Action button text (e.g., 'Sign Up', 'Learn More')."],
                            "image_prompt" => ["type" => "string", "description" => "Description for generating an ad image."]
                        ],
                        "required" => ["platform", "headline", "body", "cta", "image_prompt"]
                    ]
                ],
                "brand_package" => [
                    "type" => "object",
                    "properties" => [
                        "logo_prompt" => ["type" => "string", "description" => "Prompt to generate a logo."],
                        "color_palette" => [
                            "type" => "object",
                            "properties" => [
                                "primary" => ["type" => "string", "description" => "Hex code"],
                                "secondary" => ["type" => "string", "description" => "Hex code"],
                                "accent" => ["type" => "string", "description" => "Hex code"],
                                "background" => ["type" => "string", "description" => "Hex code"],
                                "text" => ["type" => "string", "description" => "Hex code"]
                            ],
                            "required" => ["primary", "secondary", "accent", "background", "text"]
                        ],
                        "typography" => [
                            "type" => "object",
                            "properties" => [
                                "headings" => ["type" => "string", "description" => "Font family name (e.g., Poppins)"],
                                "body" => ["type" => "string", "description" => "Font family name (e.g., Open Sans)"],
                                "accent" => ["type" => "string", "description" => "Font family name (e.g., Fira Code)"]
                            ],
                            "required" => ["headings", "body", "accent"]
                        ],
                        "brand_personality" => [
                            "type" => "array",
                            "items" => ["type" => "string"],
                            "description" => "List of personality traits (e.g., Innovative, Trustworthy)."
                        ],
                        "taglines" => [
                            "type" => "array",
                            "items" => ["type" => "string"],
                            "description" => "List of catchy taglines."
                        ]
                    ],
                    "required" => ["logo_prompt", "color_palette", "typography", "brand_personality", "taglines"]
                ],
                "landing_page" => [
                    "type" => "object",
                    "properties" => [
                        "hero_header" => ["type" => "string"],
                        "subheader" => ["type" => "string"],
                        "features_list" => [
                            "type" => "array",
                            "items" => ["type" => "string"]
                        ],
                        "cta" => ["type" => "string"]
                    ],
                    "required" => ["hero_header", "subheader", "features_list", "cta"]
                ],
                "dev_prompts" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "title" => ["type" => "string"],
                            "prompt_text" => ["type" => "string"]
                        ],
                        "required" => ["title", "prompt_text"]
                    ]
                ]
            ],
            "required" => ["ad_creatives", "brand_package", "landing_page", "dev_prompts"]
        ];

        $prompt = "Generate a complete Creative & Execution Kit for this business idea.
        
        Problem: {$problemText}
        Solution: {$solutionText}
        
        Include ad copy, branding ideas, landing page structure, and prompts for developers to build it.";

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
            Log::error("CreativeAssetGeneratorService error: " . $e->getMessage());
            return [];
        }
    }
}
