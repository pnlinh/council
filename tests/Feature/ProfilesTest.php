<?php

use App\User;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function a_user_has_a_profile()
    {
        $this->disableExceptionHandling();

        $this->get("/profiles/{$this->user->name}")
            ->assertSee($this->user->name);
    }

    /** @test */
    public function profiles_display_all_threads_created_by_the_associated_user()
    {
        $this->signIn($this->user);

        $thread = factory(Thread::class)->create([
            'user_id' => $this->user->id
        ]);

        $this->get("/profiles/{$this->user->name}")
            ->assertSee($thread->title)
            ->assertSee($thread->excerpt);
    }
}
