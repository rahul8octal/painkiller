<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\Idea;
use App\Models\Problem;
use GoToMarketMatcherService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PainNormalizerService;
use PainScorerService;
use SignalEnrichmentService;

class ProcessPainPipelineJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $problemId) {}

    /**
     * Execute the job.
     */
    public function handle(
        PainNormalizerService $normalizer,
        PainScorerService $scorer,
        SignalEnrichmentService $signals,
        GoToMarketMatcherService $matcher
    ) {
        $problem = Problem::find($this->problemId);
        if (!$problem) return;

        $norm = $normalizer->normalize($problem->body);
        $score = $scorer->score($problem->body);
        $signalData = $signals->enrich($norm['keywords'][0] ?? $problem->title);
        $matches = $matcher->match(array_merge($norm, $score, $signalData));

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
    }
}
