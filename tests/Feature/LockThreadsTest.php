<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Thread;
use App\User;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function non_admins_may_not_lock_threads()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function admins_can_lock_threads()
    {
        $this->signIn(factory(User::class)->states('admin')->create());

        $thread = factory(Thread::class)->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(200);

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    public function admins_can_unlock_threads()
    {
        $this->signIn(factory(User::class)->states('admin')->create());

        $thread = factory(Thread::class)->create([
            'user_id' => auth()->id(),
            'locked' => true
        ]);

        $this->delete(route('locked-threads.destroy', $thread))->assertStatus(200);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create(['locked' => true]);

        $this->post("{$thread->path()}/replies", [
            'body' => 'A reply',
            'user_id' => factory(User::class)->create()
        ])->assertStatus(422);
    }
}
