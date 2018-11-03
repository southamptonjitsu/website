<?php

namespace SotonJitsu\Twitter;

use GuzzleHttp\ClientInterface;
use SotonJitsu\Twitter\Cache\Cache;

class Reader
{
    private $cache;

    private $credentials;

    private $http;

    public function __construct(ClientInterface $http, Credentials $credentials, Cache $cache = null)
    {
        $this->http = $http;
        $this->credentials = $credentials;
        $this->cache = $cache;
    }

    public function getLatestTweet($userId)
    {
        $cacheKey = 'latestTweet.' . $userId;

        if ($this->cache && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $token = $this->authenticate();

        $request = (new \GuzzleHttp\Psr7\Request('GET', 'https://api.twitter.com/1.1/statuses/user_timeline.json?user_id=' . $userId . '&count=1'))
            ->withHeader('Authorization', "Bearer $token");

        $response = json_decode($this->http->send($request)->getBody()->getContents());

        if (!is_array($response) || empty($response)) {
            return '';
        }

        $tweet = $response[0]->text;

        if ($this->cache) {
            $this->cache->set($cacheKey, $tweet);
            $this->cache->save();
        }

        return $tweet;
    }

    private function authenticate()
    {
        $code = base64_encode("{$this->credentials->getId()}:{$this->credentials->getSecret()}");

        $request = (new \GuzzleHttp\Psr7\Request('POST', 'https://api.twitter.com/oauth2/token?grant_type=client_credentials'))
            ->withHeader('Authorization', "Basic $code")
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');

        return json_decode($this->http->send($request)->getBody()->getContents())->access_token;
    }
}

