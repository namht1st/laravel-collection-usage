<?php

namespace App\Http\Controllers;

use App\Http\Libs\GithubScore;
use App\Post;
use App\Tag;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CollectionController extends Controller
{

    /**
     * GitHub provides a public API endpoint that returns all of a user's recent public activity
     * The interview task was to take these events and determine a user's "GitHub Score"
     * Functions: map(), get(), sum()
     * Example: https://api.github.com/users/barryvdh/events
     * 1. Each PushEvent is worth 5 points.
     * 2. Each CreateEvent is worth 4 points.
     * 3. Each IssuesEvent is worth 3 points.
     * 4. Each CommitCommentEvent is worth 2 points.
     * 5. All other events are worth 1 point.
     */
    public function githubScore()
    {
        return $this->githubScoreWithoutCollection("barryvdh");
//        $events = collect($this->getEvents('barryvdh'));
//
//        return $events->pluck('type')->map(function ($eventType) {
//            //Collection Lookup Table
//            return collect([
//                'PushEvent'          => 5,
//                'CreateEvent'        => 4,
//                'IssuesEvent'        => 3,
//                'CommitCommentEvent' => 2,
//            ])->get($eventType, 1);
//        })->sum();

        //Use as helper functions
        return GithubScore::forUser('barryvdh');
    }

    function githubScoreWithoutCollection($username)
    {
        // Grab the events from the API, in the real world you'd probably use
        // // Guzzle or similar here, but keeping it simple for the sake of brevity.
        $events = $this->getEvents($username);
        // Get all of the event types
        $eventTypes = [];
        foreach ($events as $event) {
            $eventTypes[] = $event['type'];
        }
        // Loop over the event types and add up the corresponding scores
        $score = 0;
        foreach ($eventTypes as $eventType) {
            switch ($eventType) {
                case 'PushEvent':
                    $score += 5;
                    break;
                case 'CreateEvent':
                    $score += 4;
                    break;
                case 'IssuesEvent':
                    $score += 3;
                    break;
                case 'CommitCommentEvent':
                    $score += 2;
                    break;
                default:
                    $score += 1;
                    break;
            }
        }

        return $score;
    }

    private function getEvents($username)
    {
        $url = "https://api.github.com/users/{$username}/events";
        $client = new Client();
        $response = $client->get($url);
        $events = json_decode($response->getBody(), true);

        return $events;
    }

}
