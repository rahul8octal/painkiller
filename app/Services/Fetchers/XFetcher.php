<?php

namespace App\Services\Fetchers;

use Illuminate\Support\Facades\Http;

class XFetcher
{
    public function fetch($config)
    {

        $response = Http::get("https://nitter.net/search?f=tweets&q=startupss");



        dd($response);

      $url = "https://nitter.lacontrevoie.fr/search/rss?f=tweets&q=startups";

$response = Http::get($url);

if ($response->failed()) {
    dd("Nitter failed", $response->body());
}

$xml = simplexml_load_string($response->body());

if (!$xml) {
    dd("Invalid XML", $response->body());
}

dd($xml->channel->item);


        $response = Http::get("https://nitter.net/search?f=tweets&q=startupss");

        dump($response->body());

        $feed = simplexml_load_string($response->body());
        dd($feed);

        if (is_string($config)) {
            $config = json_decode($config, true);
        }

        if (!is_array($config)) {
            $config = ['subs' => ['Entrepreneur']];
        }

        // $token = $this->getToken();
        $subs = $config['subs'] ?? ['Entrepreneur'];
        $tweets = [];

        foreach ($subs as $sub) {



            // $token = config('services.x.bearer_token');
            // $limit = $config['limit'] ?? 10;

            $rss = "https://nitter.net/{$sub}/rss";

            //  dd($rss);

            $response = Http::get($rss);

            dd($response->body());

            if ($response->failed()) return [];

            $feed = simplexml_load_string($response->body());

            dump($feed);

            dd($feed->channel->item);


            // $response = Http::withHeaders([
            //     'Authorization' => "Bearer $token",
            // ])->get('https://api.x.com/2/tweets/search/recent', [
            //     'query' => $sub,
            //     'max_results' => $limit,
            // ])->throw()->json();

            foreach ($response['data'] ?? [] as $tweet) {
                $tweets[] = [
                    'external_id' => $tweet['id'],
                    'title' => substr($tweet['text'], 0, 80) . '...',
                    'body' => $tweet['text'],
                    'url' => "https://x.com/i/web/status/{$tweet['id']}",
                    'author' => $tweet['author_id'] ?? null,
                    'votes' => 0,
                ];
            }


            // $resp = Http::withToken($token)
            //     ->get("https://oauth.reddit.com/r/{$sub}/hot", ['limit' => 20])
            //     ->throw()->json();

            // foreach ($resp['data']['children'] ?? [] as $p) {
            //     $d = $p['data'];
            //     $out[] = [
            //         'external_id' => $d['id'],
            //         'title' => $d['title'],
            //         'body' => $d['selftext'] ?? '',
            //         'url' => "https://reddit.com" . $d['permalink'],
            //         'author' => $d['author'],
            //         'votes' => $d['ups']
            //     ];
            // }
        }
        return $tweets;
    }

    // private function getToken(): string
    // {
    //     $clientId = config('services.reddit.id');
    //     $clientSecret = config('services.reddit.secret');

    //     $basicAuth = base64_encode("$clientId:$clientSecret");




    //     $response = Http::withHeaders([
    //         'Authorization' => "Basic $basicAuth",
    //         'User-Agent' => 'MyRedditApp/1.0 (by u/EffectiveFinding8290)'
    //     ])
    //         ->asForm()
    //         ->post('https://www.reddit.com/api/v1/access_token', [
    //             'grant_type' => 'client_credentials',
    //         ]);

    //     return $response->json()['access_token'];

    // }
}
