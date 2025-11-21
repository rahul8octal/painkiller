<?php

namespace App\Console\Commands;

use App\Models\Problem;
use App\Models\Source;
use App\Services\AI\FetchProblemService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
    public function handle(FetchProblemService $fetchProblem)
    {
        $this->info('🚀 Starting weekly research ingest...');

        $source = Source::firstOrCreate(
            ['name' => 'ai_digest'],
            [
                'type' => 'ai',
                'config' => ['description' => 'AI curated community pains'],
                'active' => true,
            ]
        );

        try {
            $items = $fetchProblem->problems();
            // dd($items);
        } catch (\Throwable $e) {
            $this->error('❌ Fetch failed: ' . $e->getMessage());
            Log::error('Problem ingest failed', ['error' => $e->getMessage()]);
            return;
        }

        if (empty($items)) {
            $this->warn('No ideas returned from the AI fetch.');
            return;
        }

        $this->info('✅ Fetched ' . count($items) . ' potential pains.');

        $countNew = 0;
        foreach ($items as $i) {
            try {
                $created = Problem::firstOrCreate(
                    [
                        'source_id' => $source->id,
                        'external_id' => $i['external_id'] ?? \Illuminate\Support\Str::uuid()->toString(),
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
                Log::error('❌ Error saving Reddit problem: ' . $e->getMessage(), ['item' => $i]);
            }
        }
        $this->info('📦 Fetch complete.');
    }
}
