<?php

namespace App\Services\AI;

use App\Services\DataCollection\RedditFetcher;
use App\Services\DataCollection\StackOverflowFetcher;
use Illuminate\Support\Facades\Log;

class FetchProblemService
{
    protected array $fetchers;

    public function __construct(
        RedditFetcher $redditFetcher,
        StackOverflowFetcher $stackOverflowFetcher
    ) {
        $this->fetchers = [
            $redditFetcher,
            $stackOverflowFetcher
        ];
    }

    public function problems(int $limitPerSource = 5)
    {
        $allProblems = [];

        foreach ($this->fetchers as $fetcher) {
            try {
                $problems = $fetcher->fetch($limitPerSource);
                $allProblems = array_merge($allProblems, $problems);
            } catch (\Exception $e) {
                Log::error('Error in fetcher: ' . $e->getMessage());
            }
        }

        return $allProblems;
    }
}

// <?php

// namespace App\Services\AI;

// use ChatGpt;
// use Illuminate\Support\Facades\Log;

// class FetchProblemService
// {
//     public function problems(int $limit = 10)
//     {
//         $itemsRequested = max(5, min($limit, 10));
//         $target = 'Twitter/X (this week only)';

//         $prompt = <<<PROMPT
// You're a professional market validation and idea researcher. You look through the top platforms online where communities share problems they're facing or products they're looking for or features they'd like to see. You synthesize those and turn them into opportunities or product ideas that entrepreneurs can build.

// Focus exclusively on {$target}. Find {$itemsRequested} RAW tweet-style posts/comments from this week that highlight pain points or unmet needs. Do NOT synthesize or summarize them into ideas—return the actual (or realistically simulated) tweet title/summary and body text exactly as a user might write it.

// For example:
// - Title: 'Why is it so hard to find a plumber?' (NOT 'Plumber finding app')
// - Body: 'I called 5 places and no one picked up...' (NOT 'A platform to connect plumbers...')

// IMPORTANT: Output STRICT JSON only. Do not include conversational text. Each object must have a valid-looking Twitter/X URL, author handle, vote/like count, external ID, source, and created_utc timestamp. Use realistic data if you must simulate.
// PROMPT;

//         $schema = [
//             'type' => 'object',
//             'properties' => [
//                 'ideas' => [
//                     'type' => 'array',
//                     'items' => [
//                         'type' => 'object',
//                         'properties' => [
//                             'external_id' => ['type' => 'string'],
//                             'title' => ['type' => 'string'],
//                             'body' => ['type' => 'string'],
//                             'url' => ['type' => 'string'],
//                             'author' => ['type' => 'string'],
//                             'votes' => ['type' => 'integer'],
//                             'source' => ['type' => 'string'],
//                             'created_utc' => ['type' => 'integer'],
//                         ],
//                         'required' => ['title', 'body', 'source', 'url', 'author', 'votes', 'external_id']
//                     ]
//                 ]
//             ],
//             'required' => ['ideas']
//         ];

//         try {
//             $result = ChatGpt::generateContent($prompt, $schema);
//             Log::info('AI Raw Result: ' . (is_string($result) ? $result : json_encode($result)));

//             if (is_string($result)) {
//                 $decoded = json_decode($result, true);
//                 if (json_last_error() === JSON_ERROR_NONE) {
//                     return $decoded['ideas'] ?? [];
//                 }
//             }

//             return $result['ideas'] ?? [];
//         } catch (\Exception $e) {
//             Log::error('Error in FetchProblemService: ' . $e->getMessage());
//             return [];
//         }
//     }
// }
