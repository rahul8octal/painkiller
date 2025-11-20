<?php

namespace App\Console\Commands;

use App\Models\Problem;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Fetchers\RedditFetcher;
use App\Services\Fetchers\XFetcher;

class FetchProblem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-problem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(RedditFetcher $redditFetcher, XFetcher $xFetcher)
    {
            $this->info('🚀 Starting Reddit problem fetch...');

        // Ensure a source record exists in the 'sources' table
        $source = Source::firstOrCreate(
            ['name' => 'reddit'],
            [
                'type' => 'api',
                'config' => [
                    'subs' => ['Entrepreneur', 'Startups'], // default subs
                ],
                'active' => true,
            ]
        );

        $config = $source->config;

        // 1️⃣ Fetch data from Reddit API using your RedditFetcher service
        try {
            // $items = $redditFetcher->fetch($config);
            $items = $xFetcher->fetch($config);

            dd($items);
          
        } catch (\Throwable $e) {
            $this->error('❌ Reddit API fetch failed: '.$e->getMessage());
            Log::error('Reddit fetch error', ['error' => $e->getMessage()]);
            return;
        }

        $this->info('✅ Fetched '.count($items).' Reddit posts.');
        $this->info($items);


        // 2️⃣ Store or update each Reddit post in 'problems' table
        $countNew = 0;
        foreach ($items as $i) {
            try {
                $created = Problem::firstOrCreate(
                    [
                        'source_id' => $source->id,
                        'external_id' => $i['external_id'],
                    ],
                    [
                        'title' => $i['title'] ?? '(No Title)',
                        'body' => $i['body'] ?? '',
                        'url' => $i['url'] ?? '',
                        'author' => $i['author'] ?? '',
                        'votes' => $i['votes'] ?? 0,
                        'status' => 'raw',
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $countNew++;
                }
            } catch (\Throwable $e) {
                Log::error('❌ Error saving Reddit problem: '.$e->getMessage(), ['item' => $i]);
            }
        }

        $this->info("🎯 $countNew new Reddit problems saved successfully!");
        $this->info('📦 Fetch complete.');
    }
}
