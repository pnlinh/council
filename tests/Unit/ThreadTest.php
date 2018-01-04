<?php

namespace Tests\Unit;

use App\User;
use App\Reply;
use App\Thread;
use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->thread = factory(Thread::class)->create();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function a_thread_has_a_string_path()
    {
        $thread = factory(Thread::class)->create();

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    /** @test */
    public function a_thread_has_a_creator()
    {
        $this->assertInstanceOf('App\User', $this->thread->creator);
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 2
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = factory(Thread::class)->create();
        $this->assertInstanceOf(Channel::class, $thread->channel);
    }

    /** @test */
    public function a_thread_can_be_subscribed_to()
    {
        $this->signIn($this->user);

        $this->thread->subscribe();

        $this->assertEquals(
            1,
            $this->thread->subscriptions()->where('user_id', auth()->id())->count()
        );
    }

    /** @test */
    public function a_thread_can_be_unsubscribed_from()
    {
        $this->signIn($this->user);

        $this->thread->subscribe();

        $this->thread->unsubscribe();

        $this->assertEquals(
            0,
            $this->thread->subscriptions()->where('user_id', auth()->id())->count()
        );
    }

    /** @test */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $this->signIn($this->user);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribedTo);
    }

    /** @test */
    public function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn($this->user);

        $this->assertTrue($this->thread->hasUpdatesFor());

        $this->get($this->thread->path());

        $this->assertFalse($this->thread->hasUpdatesFor());
    }

    /** @test */
    public function a_threads_body_is_sanitized_automatically()
    {
        $thread = factory(Thread::class)->make(['body' => '<script>alert("gotcha");</script>']);

        $this->assertEmpty($thread->body);
    }
}
