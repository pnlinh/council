<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get($count = 5)
    {
        return array_map('json_decode', Redis::zrevrange($this->getCacheKey(), 0, ($count - 1)));
    }

    public function push($thread)
    {
        Redis::zincrby($this->getCacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    public function clear()
    {
        Redis::del($this->getCacheKey());
    }

    protected function getCacheKey()
    {
        return app()->environment('testing') ? 'test_trending_threads' : 'trending_threads';
    }
}
