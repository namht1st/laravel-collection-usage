<?php


namespace App\Http\Libs;


use GuzzleHttp\Client;

class GithubScore
{

    private $username;

    private function __construct($username)
    {
        $this->username = $username;
    }

    public static function forUser($username)
    {
        return (new self($username))->score();
    }

    private function score()
    {
        return $this->events()->pluck('type')->map(function ($eventType) {
            return $this->lookupScore($eventType);
        })->sum();
    }

    private function events()
    {
        $url = "https://api.github.com/users/{$this->username}/events";
        $client = new Client();

        return collect(json_decode($client->get($url)->getBody(), true));
    }

    private function lookupScore($eventType)
    {
        return collect([
            'PushEvent'          => 5,
            'CreateEvent'        => 4,
            'IssuesEvent'        => 3,
            'CommitCommentEvent' => 2,
        ])->get($eventType, 1);
    }


}
