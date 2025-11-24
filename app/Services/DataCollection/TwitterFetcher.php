<?php

namespace App\Services\DataCollection;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class TwitterFetcher implements DataCollectionServiceInterface
{
    // List of Nitter instances to rotate through
    protected array $instances = [
        'https://nitter.net',
        'https://nitter.cz',
        'https://nitter.privacydev.net',
        'https://nitter.poast.org'
    ];

    protected array $searchQueries = [
        '"I hate"',
        '"wish there was"',
        '"annoying"',
        '"pain point"',
        '"struggling with"',
        '"worst part of"'
    ];

    public function fetch(int $limit = 10): array
    {
        $allTweets = [];
        $instance = $this->getRandomInstance();

        foreach ($this->searchQueries as $query) {
            try {
                // Nitter RSS search endpoint
                $url = "{$instance}/search/rss?f=tweets&q=" . urlencode($query);

                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $xml = new SimpleXMLElement($response->body());

                    foreach ($xml->channel->item as $item) {
                        $description = (string) $item->description;
                        // Basic cleanup of HTML tags often found in RSS descriptions
                        $body = strip_tags($description);

                        // Skip if too short
                        if (strlen($body) < 20) continue;

                        $allTweets[] = [
                            'external_id' => 'twitter_' . md5((string) $item->guid),
                            'title' => 'Twitter Rant: ' . substr($body, 0, 50) . '...',
                            'body' => $body,
                            'url' => (string) $item->link,
                            'author' => (string) $item->children('dc', true)->creator,
                            'votes' => 0, // RSS doesn't give vote counts easily
                            'source' => 'twitter',
                            'created_utc' => strtotime((string) $item->pubDate),
                        ];
                    }
                } else {
                    Log::warning("Failed to fetch from Nitter instance {$instance}: " . $response->status());
                    // Try one more instance if first fails
                    $instance = $this->getRandomInstance();
                }
            } catch (\Exception $e) {
                Log::error("Error fetching from Twitter (Nitter): " . $e->getMessage());
            }
        }

        // Shuffle and limit
        shuffle($allTweets);
        return array_slice($allTweets, 0, $limit);
    }

    protected function getRandomInstance(): string
    {
        return $this->instances[array_rand($this->instances)];
    }
}
