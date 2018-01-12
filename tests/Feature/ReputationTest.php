<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\User;
use App\Reputation;
use App\Reply;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_gains_points_when_they_create_a_thread()
    {
        $thread = factory(Thread::class)->create();

        $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_they_delete_a_thread()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create(['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->creator->fresh()->reputation);
    }

    /** @test */
    public function a_earns_points_when_they_reply_to_a_thread()
    {
        $thread = factory(Thread::class)->create();

        $reply = $thread->addReply([
            'user_id' => factory(User::class)->create()->id,
            'body' => 'A reply',
        ]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_reply_to_a_thread_is_delete()
    {
        $this->signIn();

        $reply = factory(Reply::class)->create(['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->fresh()->reputation);

        $this->delete("/replies/{$reply->id}");

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /** @test */
    public function a_user_earns_points_when_their_reply_is_marked_as_best()
    {
        $thread = factory(Thread::class)->create();

        $reply = $thread->addReply([
            'user_id' => factory(User::class)->create()->id,
            'body' => 'A reply',
        ]);

        $thread->markBestReply($reply);

        $total = Reputation::BEST_REPLY_AWARDED + Reputation::REPLY_POSTED;
        $this->assertEquals($total, $reply->owner->reputation);
    }

    /** @test */
    public function a_user_earns_points_when_their_reply_is_favorited()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create();

        $reply = $thread->addReply([
            'user_id' => factory(User::class)->create()->id,
            'body' => 'A reply',
        ]);

        $this->post("/replies/{$reply->id}/favorites");

        $total = Reputation::REPLY_FAVORITED + Reputation::REPLY_POSTED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_reply_is_unfavorited()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create();

        $reply = $thread->addReply([
            'user_id' => factory(User::class)->create()->id,
            'body' => 'A reply',
        ]);

        $this->post("/replies/{$reply->id}/favorites");

        $total = Reputation::REPLY_FAVORITED + Reputation::REPLY_POSTED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);

        $this->delete("/replies/{$reply->id}/favorites");

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->fresh()->reputation);
    }
}
