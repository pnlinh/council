<?php

namespace App;

use App\Channel;
use App\Events\ThreadHasNewReply;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Stevebauman\Purify\Facades\Purify;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();

            Reputation::reduce($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);

            Reputation::award($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
        });
    }

    protected $casts = [
        'locked' => 'boolean'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadHasNewReply($this, $reply));

        return $reply;
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function getExcerptAttribute()
    {
        return str_limit($this->body, 250);
    }

    public function getBodyAttribute($value)
    {
        return Purify::clean($value);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function hasUpdatesFor()
    {
        return $this->updated_at > cache(auth()->user()->visitedThreadCacheKey($this));
    }

    public function visits()
    {
        return new Visits($this);
    }

    public function setSlugAttribute($value)
    {
        if (static::whereSlug($slug = str_slug($value))->exists()) {
            $slug = "{$slug}-{$this->id}";
        }

        $this->attributes['slug'] = $slug;
    }

    public function markBestReply($reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
        Reputation::award($reply->owner, Reputation::BEST_REPLY_AWARDED);
    }

    public function resolvedStatus()
    {
        return $this->best_reply_id !== null ? 'Resolved' : 'Unresolved';
    }

    public function toSearchableArray()
    {
        return $this->toArray() + [
            'path' => $this->path(),
            'resolved_status' => $this->resolvedStatus(),
        ];
    }
}
