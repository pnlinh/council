<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->thread = factory(Thread::class)->create();
    }

    /** @test */
    public function a_user_can_browse_threads()
    {
        $thread = factory(Thread::class)->create([
            'title' => 'My Test Thread'
        ]);

        $this->get('/threads')
            ->assertSee('My Test Thread');
    }

    /** @test */
    public function a_user_can_view_a_single_thread()
    {
        $thread = factory(Thread::class)->create([
            'title' => 'My Test Thread'
        ]);

        $this->user->threads()->save($thread);

        $response = $this->get($thread->path());

        $response->assertSee('My Test Thread');
        $response->assertSee($this->user->name);
    }

    /** @test */
    public function a_user_can_filter_threads_by_channel()
    {
        $this->disableExceptionHandling();

        $channel = factory(Channel::class)->create([
            'slug' => 'my-channel'
        ]);

        $threadInChannel = factory(Thread::class)->create([
            'channel_id' => $channel->id
        ]);

        $threadNotInChannel = factory(Thread::class)->create();

        $this->get("/threads/{$channel->slug}")
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_username()
    {
        $user = factory(User::class)->create(['name' => 'FooBar']);
        $this->signIn($user);

        $usersThread = factory(Thread::class)->create([
            'user_id' => auth()->id()
        ]);
        $otherThread = factory(Thread::class)->create();

        $this->get("threads?by=FooBar")
            ->assertSee($usersThread->title)
            ->assertDontSee($otherThread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = factory(Thread::class)->create();
        $threadWithThreeReplies = factory(Thread::class)->create();
        $threadWithNoReplies = $this->thread;

        factory(Reply::class, 3)->create(['thread_id' => $threadWithThreeReplies->id]);
        factory(Reply::class, 2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3,2,0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $threadWithNoReplies = $this->thread;
        $threadWithReplies = factory(Thread::class)->create();

        factory(Reply::class, 2)->create(['thread_id' => $threadWithReplies->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $replies = factory(Reply::class, 2)->create(['thread_id' => $this->thread->id]);

        $response = $this->getJson($this->thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    /** @test */
    public function we_record_a_new_visit_each_time_a_thread_is_read()
    {
        $this->thread->visits()->reset();

        $this->assertSame(0, $this->thread->visits()->count());

        $this->call('GET', $this->thread->path());

        $this->assertEquals(1, $this->thread->visits()->count());
    }
}
