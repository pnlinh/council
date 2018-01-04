<?php
namespace Tests\Feature;

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->thread = factory(Thread::class)->create();
    }

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn($this->user);

        $this->post($this->thread->path() . '/subscriptions');

        $this->assertCount(1, $this->thread->fresh()->subscriptions);
    }

    /** @test */
    public function a_user_can_unsubscribe_from_a_thread()
    {
        $this->signIn($this->user);

        $this->thread->subscribe();

        $this->delete($this->thread->path() . '/subscriptions');

        $this->assertFalse($this->thread->isSubscribedTo);
    }
}
