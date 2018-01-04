<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->thread = factory(Thread::class)->create();
    }

    /** @test */
    public function an_authenticated_user_may_reply_to_a_thread()
    {
        $this->disableExceptionHandling();

        $this->signIn($this->user);

        $reply = factory(Reply::class)->make([
            'body' => 'Plz die',
        ]);

        $this->post("{$this->thread->path()}/replies", $reply->toArray());

        $this->assertDatabaseHas('replies', [
            'thread_id' => $this->thread->id,
            'user_id' => $this->user->id,
            'body' => 'Plz die',
        ]);

        $this->assertEquals(1, $this->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthenticated_user_may_not_reply_to_a_thread()
    {
        $this->post("{$this->thread->path()}/replies", [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function a_reply_must_have_a_body()
    {
        $this->signIn($this->user);

        $response = $this->post("{$this->thread->path()}/replies", ['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_a_reply()
    {
        $reply = factory(Reply::class)->create();

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('/login');

        $this->signIn($this->user)
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_a_reply()
    {
        $this->signIn($this->user);

        $reply = factory(Reply::class)->create([
            'user_id' => auth()->id()
        ]);

        $this->delete("/replies/{$reply->id}")
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthorized_users_cannot_update_a_reply()
    {
        $reply = factory(Reply::class)->create();

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('/login');

        $this->signIn($this->user)
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_update_a_reply()
    {
        $this->signIn($this->user);

        $reply = factory(Reply::class)->create([
            'user_id' => auth()->id()
        ]);

        $this->patch("/replies/{$reply->id}", ['body' => 'Updated body']);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'Updated body']);
    }

    /** @test */
    public function replies_that_contain_spam_may_not_be_created()
    {
        // $this->disableExceptionHandling();

        $this->signIn($this->user);

        $reply = factory(Reply::class)->make([
            'body' => 'Yahoo Customer Support',
        ]);

        $this->json("post", "{$this->thread->path()}/replies", $reply->toArray())
            ->assertStatus(422);
    }

    /** @test */
    public function users_may_only_reply_once_per_minute()
    {
        // $this->disableExceptionHandling();

        $this->signIn($this->user);

        $reply = factory(Reply::class)->make([
            'body' => 'My simple reply',
        ]);

        $this->post("{$this->thread->path()}/replies", $reply->toArray())
            ->assertStatus(200);

        $this->post("{$this->thread->path()}/replies", $reply->toArray())
            ->assertStatus(429);
    }
}
