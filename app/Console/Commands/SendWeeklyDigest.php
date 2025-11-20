<?php

namespace App\Console\Commands;

use App\Models\Idea;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendWeeklyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $topIdeas = Idea::where('review_status', 'approved')
            ->whereHas('problem', fn($q) => $q->where('total_score', '>', 7))
            ->latest()->take(5)->get();

        $html = view('emails.weekly', compact('topIdeas'))->render();

        Http::withToken(env('RESEND_API_KEY'))
            ->post('https://api.resend.com/emails', [
                'from' => 'Painkiller <noreply@painkiller.ai>',
                'to' => ['newsletter@painkiller.ai'],
                'subject' => 'Top Painkiller Ideas of the Week',
                'html' => $html
            ]);
    }
}
