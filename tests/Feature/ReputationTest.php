<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\User;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_gains_points_when_they_create_a_thread()
    {
        $thread = factory(Thread::class)->create();

        $this->assertEquals(10, $thread->creator->reputation);
    }

    /** @test */
    public function a_earns_points_when_they_reply_to_a_thread()
    {
        $thread = factory(Thread::class)->create();

        $reply = $thread->addReply([
            'user_id' => factory(User::class)->create()->id,
            'body' => 'A reply',
        ]);

        $this->assertEquals(2, $reply->owner->reputation);
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

        $this->assertEquals(52, $reply->owner->reputation);
    }
}
