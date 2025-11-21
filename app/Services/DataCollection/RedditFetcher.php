<?php

namespace App\Services\DataCollection;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedditFetcher implements DataCollectionServiceInterface
{
    protected string $baseUrl = 'https://www.reddit.com';
    protected array $subreddits = ['startups', 'entrepreneur', 'smallbusiness', 'ideas'];

    public function fetch(int $limit = 10): array
    {
        $allPosts = [];

        foreach ($this->subreddits as $subreddit) {
            try {
                $response = Http::withUserAgent('PainkillerIdeasBot/1.0')->get("{$this->baseUrl}/r/{$subreddit}/hot.json", [
                    'limit' => 5 // Fetch a few from each to mix
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $posts = $data['data']['children'] ?? [];

                    foreach ($posts as $post) {
                        $postData = $post['data'];
                        
                        // Basic filtering: must be a text post and have some engagement
                        if (!$postData['is_self'] || $postData['ups'] < 5) {
                            continue;
                        }

                        $allPosts[] = [
                            'external_id' => 'reddit_' . $postData['id'],
                            'title' => $postData['title'],
                            'body' => $postData['selftext'],
                            'url' => "{$this->baseUrl}{$postData['permalink']}",
                            'author' => $postData['author'],
                            'votes' => $postData['ups'],
                            'source' => 'reddit',
                            'created_utc' => $postData['created_utc'],
                        ];
                    }
                } else {
                    Log::warning("Failed to fetch from Reddit r/{$subreddit}: " . $response->status());
                }
            } catch (\Exception $e) {
                Log::error("Error fetching from Reddit r/{$subreddit}: " . $e->getMessage());
            }
        }

        // Shuffle and limit
        shuffle($allPosts);
        return array_slice($allPosts, 0, $limit);
    }
}
