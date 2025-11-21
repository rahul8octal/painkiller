<?php

namespace App\Services\Signals;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SignalEnrichmentService
{
    public function enrich($keywords): array
    {
        $primary = $this->normalizeKeywords($keywords);

        return [
            'search_volume' => $this->googleTrendsApprox($primary),
            'reddit_mentions' => $this->redditMentions($primary),
            'github_issues' => $this->githubIssueCount($primary),
            'community_index' => rand(40, 95), // Simulated score 0-100
            'keyword_trends' => $this->simulateKeywordTrends($primary),
            'validation_links' => $this->simulateValidationLinks($primary),
        ];
    }

    private function googleTrendsApprox(?string $kw): int
    {
        if (!$kw) {
            return 0;
        }

        return rand(10, 1000); // placeholder until a real API is wired up
    }

    private function redditMentions(?string $kw): int
    {
        if (!$kw) {
            return 0;
        }

        try {
            $response = Http::timeout(5)->get('https://www.reddit.com/search.json', [
                'q' => $kw,
                'limit' => config('services.signals.default_limit', 20),
            ]);
        } catch (\Throwable $e) {
            return 0;
        }

        $res = $response->json();

        return count($res['data']['children'] ?? []);
    }

    private function githubIssueCount(?string $kw): int
    {
        if (!$kw) {
            return 0;
        }

        // optionally call GitHub search API in the future
        return 0;
    }

    private function normalizeKeywords($keywords): ?string
    {
        if (is_array($keywords)) {
            $keywords = array_filter(array_map('trim', $keywords));
            if (empty($keywords)) {
                return null;
            }

            return implode(' ', array_slice($keywords, 0, 3));
        }

        $value = trim((string) $keywords);

        return strlen($value) ? Str::limit($value, 200) : null;
    }

    private function simulateKeywordTrends($kw): array
    {
        // Simulating the "Keyword Traffic & Trends" chart data
        return [
            'total_monthly_searches' => rand(500, 5000),
            'trend_direction' => 'up', // or 'down', 'stable'
            'top_keywords' => [
                ['keyword' => $kw . ' tools', 'volume' => rand(100, 500), 'cpc' => rand(1, 10)],
                ['keyword' => 'best ' . $kw, 'volume' => rand(50, 300), 'cpc' => rand(1, 5)],
                ['keyword' => 'how to ' . $kw, 'volume' => rand(200, 800), 'cpc' => rand(0, 2)],
            ]
        ];
    }

    private function simulateValidationLinks($kw): array
    {
        // Simulating "Example Companies" / Research Sources bubbles
        $sources = ['reddit.com', 'twitter.com', 'indiehackers.com', 'news.ycombinator.com'];
        $links = [];

        for ($i = 0; $i < 5; $i++) {
            $source = $sources[array_rand($sources)];
            $links[] = [
                'source' => $source,
                'url' => "https://{$source}/search?q=" . urlencode($kw),
                'title' => "Discussion about {$kw} on {$source}"
            ];
        }

        return $links;
    }
}
