<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Idea;
use App\Models\Problem;
use App\Services\AI\GoToMarketMatcherService;
use App\Services\AI\PainNormalizerService;
use App\Services\AI\PainScorerService;
use App\Services\Signals\SignalEnrichmentService;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;

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
        GoToMarketMatcherService $matcher
    ) {
        $problem = Problem::find(6);
        if (!$problem) return;

        $norm = $normalizer->normalize($problem->body);
        $score = $scorer->score($problem->body);
        $signalData = $signals->enrich($norm['keywords'] ?? $problem->title);
        $matches = $matcher->match(array_merge($norm, $score, $signalData));


        // $norm = [
        //     'summary' => 'Community post inviting founders to share their startups via a structured template and lifecycle stages to facilitate feedback, networking, and resource sharing while prohibiting public fundraising.',
        //     'industry' => 'Startups and entrepreneurship',
        //     'keywords' => [
        //         'r/startups',
        //         'Reddit',
        //         'startup template',
        //         'elevator pitch',
        //         'MVP',
        //         'product validation',
        //         'product market fit',
        //         'startup lifecycle',
        //         'Discovery',
        //         'Validation',
        //         'Efficiency',
        //         'Scaling',
        //         'Profit Maximization',
        //         'Renewal',
        //         'networking',
        //         'goals',
        //         'discount',
        //         'Q4 2023',
        //     ],
        //     'sentiment' => 'neutral',
        // ];


        // $score = [
        //     'urgency' => 3,
        //     'frequency' => 3,
        //     'willingness_to_pay' => 2,
        // ];
        // $signalData = [
        //     'search_volume' => 537,
        //     'reddit_mentions' => 20,
        //     'github_issues' => 0,
        // ];

    

        $problem->update([
            'tags' => $norm['keywords'] ?? [],
            'scores' => $score,
            'signals' => $signalData,
            'total_score' => collect($score)->avg(),
            'status' => 'matched'
        ]);

        Idea::create([
            'problem_id' => $problem->id,
            'structured' => $norm['summary'] ?? '',
            'solution' => json_encode($matches['plays'] ?? []),
            'complexity' => 'medium',
            'review_status' => 'pending'
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

        info("Processed Problem ID {$problem->id}: Score {$problem->total_score}");
    }
}
