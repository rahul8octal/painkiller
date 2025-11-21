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
}
