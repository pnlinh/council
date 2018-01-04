<?php

namespace App\Listeners;

use App\User;
use App\Events\ThreadHasNewReply;
use App\Notifications\YouWereMentioned;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMentionedUsers implements ShouldQueue
{
    public function handle(ThreadHasNewReply $event)
    {
        collect($event->reply->mentionedUsers())
            ->map(function ($name) {
                return User::where('name', $name)->first();
            })
            ->filter()
            ->each
            ->notify(new YouWereMentioned($event->reply));
    }
}
