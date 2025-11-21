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
//         // Request a mix of platforms
//         $target = "a mix of top online platforms including Reddit, Twitter/X, and StackOverflow";

//         $prompt = "You're a professional market validation and idea researcher. You look through the top platforms online where communities share problems they're facing or products they're looking for or features they'd like to see.
        
//         I want you to find 5-10 RAW user posts/comments that represent 'pain points' or 'problems'. 
//         Do NOT synthesize them into business ideas yet. I want the ACTUAL (or realistically simulated) post title and post body as if a user wrote it.
        
//         For example:
//         - Title: 'Why is it so hard to find a plumber?' (NOT 'Plumber finding app')
//         - Body: 'I called 5 places and no one picked up...' (NOT 'A platform to connect plumbers...')
        
//         Focus on finding a mix of these from {$target}.

//         IMPORTANT: Output STRICT JSON only. Do not include any conversational text. You MUST provide a valid URL, author name, vote count, and external ID for every item. If simulating data, ensure these look realistic (e.g., valid-looking Reddit/Twitter URLs).";

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

