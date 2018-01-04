<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function record()
    {
        Redis::incr($this->cacheKey());

        return $this;
    }

    public function count()
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }

    protected function cacheKey()
    {
        return "threads.{$this->entity->id}.visits";
    }
}
