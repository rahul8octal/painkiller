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
