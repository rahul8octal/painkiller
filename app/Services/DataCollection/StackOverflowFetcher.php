<?php

namespace App\Services\DataCollection;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StackOverflowFetcher implements DataCollectionServiceInterface
{
    protected string $baseUrl = 'https://api.stackexchange.com/2.3';

    public function fetch(int $limit = 10): array
    {
        $posts = [];

        try {
            // Searching for questions with "problem", "issue", "struggle" in title/body
            // and tagged with relevant tags if needed. 
            // Using 'search/advanced' for more control.
            $response = Http::get("{$this->baseUrl}/search/advanced", [
                'order' => 'desc',
                'sort' => 'votes',
                'q' => 'problem struggle "how do i"', // Generic search for problems
                'site' => 'stackoverflow',
                'pagesize' => $limit,
                'filter' => 'withbody' // Include body in response
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $items = $data['items'] ?? [];

                foreach ($items as $item) {
                    $posts[] = [
                        'external_id' => 'so_' . $item['question_id'],
                        'title' => $item['title'],
                        'body' => $item['body_markdown'] ?? $item['body'] ?? '', // Prefer markdown if available (needs filter tweak?) - default body is HTML
                        'url' => $item['link'],
                        'author' => $item['owner']['display_name'] ?? 'Anonymous',
                        'votes' => $item['score'],
                        'source' => 'stackoverflow',
                        'created_utc' => $item['creation_date'],
                    ];
                }
            } else {
                Log::warning("Failed to fetch from StackOverflow: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error fetching from StackOverflow: " . $e->getMessage());
        }

        return $posts;
    }
}
