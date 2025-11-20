<?php

namespace App\Services\Fetchers;

use Illuminate\Support\Facades\Http;

class RedditFetcher
{
    public function fetch($config)
    {

        if (is_string($config)) {
            $config = json_decode($config, true);
        }

        if (!is_array($config)) {
            $config = ['subs' => ['Entrepreneur']];
        }

        $token = $this->getToken();
        $subs = $config['subs'] ?? ['Entrepreneur'];
        $out = [];


        foreach ($subs as $sub) {

            $resp = Http::withToken($token)
                ->get("https://oauth.reddit.com/r/{$sub}/hot", ['limit' => 20])
                ->throw()->json();

            foreach ($resp['data']['children'] ?? [] as $p) {
                $d = $p['data'];
                $out[] = [
                    'external_id' => $d['id'],
                    'title' => $d['title'],
                    'body' => $d['selftext'] ?? '',
                    'url' => "https://reddit.com" . $d['permalink'],
                    'author' => $d['author'],
                    'votes' => $d['ups']
                ];
            }
        }
        return $out;
    }

    private function getToken(): string
    {
        $clientId = config('services.reddit.id');
        $clientSecret = config('services.reddit.secret');

        $basicAuth = base64_encode("$clientId:$clientSecret");

        $response = Http::withHeaders([
            'Authorization' => "Basic $basicAuth",
            'User-Agent' => 'MyRedditApp/1.0 (by u/EffectiveFinding8290)'
        ])
            ->asForm()
            ->post('https://www.reddit.com/api/v1/access_token', [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json()['access_token'];

    }
}

// title : Share your startup - quarterly post

// body : Share Your Startup - Q4 2023

// [**r/startups**](https://www.reddit.com/r/startups/) **wants to hear what you're working on!**

// Tell us about your startup in a comment within this submission. Follow this template:
// =====================================================================================

// ​

// *   **Startup Name / URL**
// *   **Location of Your Headquarters**
//     *   Let people know where you are based for possible local networking with you and to share local resources with you
// *   **Elevator Pitch/Explainer Video**
// *   **More details:**
//     *   What life cycle stage is your startup at? (reference the stages below)
//     *   Your role?
// *   **What goals are you trying to reach this month?**
//     *   How could [r/startups](https://www.reddit.com/r/startups/) help?
//     *   Do **NOT** solicit funds publicly--this may be illegal for you to do so
// *   **Discount for** [r/startups](/r/startups) **subscribers?**
//     *   Share how our community can get a discount

// ​


// \--------------------------------------------------

// ​

// **Startup Life Cycle Stages** (Max Marmer life cycle model for startups as used by Startup Genome and Kauffman Foundation)

// **Discovery**

// *   Researching the market, the competitors, and the potential users
// *   Designing the first iteration of the user experience
// *   Working towards problem/solution fit (Market Validation)
// *   Building MVP

// ​

// **Validation**

// *   Achieved problem/solution fit (Market Validation)
// *   MVP launched
// *   Conducting Product Validation
// *   Revising/refining user experience based on results of Product Validation tests
// *   Refining Product through new Versions (Ver.1+)
// *   Working towards product/market fit

// ​

// **Efficiency**

// *   Achieved product/market fit
// *   Preparing to begin the scaling process
// *   Optimizing the user experience to handle aggressive user growth at scale
// *   Optimizing the performance of the product to handle aggressive user growth at scale
// *   Optimizing the operational workflows and systems in preparation for scaling
// *   Conducting validation tests of scaling strategies

// ​

// **Scaling**

// *   Achieved validation of scaling strategies
// *   Achieved an acceptable level of optimization of the operational systems
// *   Actively pushing forward with aggressive growth
// *   Conducting validation tests to achieve a repeatable sales process at scale

// ​

// **Profit Maximization**

// *   Successfully scaled the business and can now be considered an established company
// *   Expanding production and operations in order to increase revenue
// *   Optimizing systems to maximize profits

// ​

// **Renewal**

// *   Has achieved near-peak profits
// *   Has achieved near-peak optimization of systems
// *   Actively seeking to reinvent the company and core products to stay innovative
// *   Actively seeking to acquire other companies and technologies to expand market share and relevancy
// *   Actively exploring horizontal and vertical expansion to increase prevent the decline of the company




//url: https://reddit.com/r/startups/comments/1o40sqm/share_your_startup_quarterly_post/


//votes: 10

// tags : ["r/startups", "Reddit", "startup template", "elevator pitch", "MVP", "product validation", "product market fit", "startup lifecycle", "Discovery", "Validation", "Efficiency", "Scaling", "Profit Maximization", "Renewal", "networking", "goals", "discount", "Q4 2023"]

// signals : {"github_issues": 0, "search_volume": 537, "reddit_mentions": 20}

// scores : {"urgency": 3, "frequency": 3, "willingness_to_pay": 2}

// total_score : 3







    
// structured
// Community post inviting founders to share their startups via a structured template and lifecycle stages to facilitate feedback, networking, and resource sharing while prohibiting public fundraising.


//solutions
// [{"title":"Lifecycle Showcase Threads (Template-Driven UGC)","type":"Community","short_reason":"Leverages Reddit-friendly, structured posts by lifecycle stage to drive founder participation, peer feedback, and networking without fundraising content."},{"title":"Founder Feedback Circles by Stage","type":"Community","short_reason":"Creates small, stage-matched groups (Discovery, Validation, PMF, etc.) for recurring feedback sessions that deepen engagement and improve product validation."},{"title":"Q4 Startup Perks Roundup (Discount & Resource Swap)","type":"Marketing","short_reason":"Aggregates relevant discounts and resources by lifecycle stage to attract low-WTP founders, increase thread utility, and boost shareability and search interest."}]