<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function a_user_has_threads()
    {
        $this->user->threads()->save(factory(Thread::class)->create());

        $this->assertInstanceOf('App\Thread', $this->user->threads()->first());
    }

    /** @test */
    public function a_user_has_replies()
    {
        $this->user->replies()->save(factory(Reply::class)->create());

        $this->assertInstanceOf('App\Reply', $this->user->replies()->first());
    }

    /** @test */
    public function a_user_can_fetch_their_most_recent_reply()
    {
        $reply = factory(Reply::class)->create(['user_id' => $this->user->id]);

        $this->assertEquals($reply->id, $this->user->lastReply->id);
    }

    /** @test */
    public function a_user_can_determine_their_avatar_path()
    {
        $this->assertEquals(asset('avatars/placeholder.png'), $this->user->avatar_path);

        $this->user->avatar_path = 'me.jpg';

        $this->assertEquals(asset('me.jpg'), $this->user->avatar_path);
    }
}
