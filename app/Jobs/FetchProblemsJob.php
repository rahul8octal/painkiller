<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchProblemsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */

    public function handle(RedditFetcher $rf, StackOverflowFetcher $sf, XFetcher $xf) {
        
        $items = array_merge($rf->fetch([...]), $sf->fetch([...]), $xf->fetch([...]));
        foreach($items as $i) {
            Problem::firstOrCreate(
                ['source_id'=>1,'external_id'=>$i['external_id']],
                ['title'=>$i['title'],'body'=>$i['body'],'url'=>$i['url'],'author'=>$i['author'],'votes'=>$i['votes'],'status'=>'raw']
            );
        }
    }
    
}
