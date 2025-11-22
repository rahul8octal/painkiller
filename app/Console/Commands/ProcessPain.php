<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Idea;
use App\Models\Problem;
use App\Services\AI\CommunitySignalsService;
use App\Services\AI\GoToMarketMatcherService;
use App\Services\AI\PainNormalizerService;
use App\Services\AI\PainScorerService;
use App\Services\AI\RevenueProjectionService;
use App\Services\AI\CreativeAssetGeneratorService;
use App\Services\AI\KeywordTrafficService;
use App\Services\Signals\SignalEnrichmentService;
use Illuminate\Console\Command;

class ProcessPain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-pain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        PainNormalizerService $normalizer,
        PainScorerService $scorer,
        SignalEnrichmentService $signals,
        GoToMarketMatcherService $matcher,
        RevenueProjectionService $revenue,
        CreativeAssetGeneratorService $creative,
        KeywordTrafficService $traffic,
        CommunitySignalsService $community
    ) {
        $problems = Problem::where('status', 'raw')->get();

        $this->info("Found " . $problems->count() . " raw problems to process.");

        foreach ($problems as $problem) {
            try {
                $this->info("Processing Problem ID {$problem->id}...");

                $norm = $normalizer->normalize($problem->body);
                $score = $scorer->score($problem->body) ?? [];

                // Use title if keywords are missing or empty
                $keywords = $norm['keywords'] ?? [];
                $searchText = !empty($keywords) ? implode(' ', $keywords) : $problem->title;

                $signalData = $signals->enrich($searchText);
                $matches = $matcher->match(array_merge($norm, $score, $signalData));

                $revenueData = $revenue->predict($problem->body, $score);
                $creativeData = $creative->generate($problem->body, $norm['summary'] ?? '');
                $trafficData = $traffic->analyze($problem->body, $norm['keywords'] ?? []);
                $communityData = $community->analyze($problem->body, $norm['keywords'] ?? []);

                $problem->update([
                    'tags' => $norm['keywords'] ?? [],
                    'scores' => $score,
                    'signals' => $signalData,
                    'total_score' => collect($score)->avg() ?? 0,
                    'status' => 'matched'
                ]);

                Idea::create([
                    'problem_id' => $problem->id,
                    'structured' => $norm['summary'] ?? '',
                    'solution' => json_encode($matches['plays'] ?? []),
                    'complexity' => 'medium',
                    'review_status' => 'pending',
                    'revenue_potential' => $revenueData,
                    'market_validation' => [
                        'community_index' => $signalData['community_index'] ?? 0,
                        'keyword_trends' => $signalData['keyword_trends'] ?? [],
                        'validation_links' => $signalData['validation_links'] ?? [],
                        'keyword_traffic' => $trafficData,
                        'community_signals' => $communityData
                    ],
                    'creative_assets' => $creativeData
                ]);

                AuditLog::create([
                    'action' => 'AI_PROCESS',
                    'details' => [
                        'score' => $problem->total_score,
                        'keywords' => $problem->tags
                    ],
                    'auditable_type' => Problem::class,
                    'auditable_id' => $problem->id
                ]);

                $this->info("Processed Problem ID {$problem->id}: Score {$problem->total_score}");
            } catch (\Exception $e) {
                $this->error("Failed to process Problem ID {$problem->id}: " . $e->getMessage());
            }
        }

        $this->info("Processing complete.");
    }
}
