<?php

namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use App\Channel;
use App\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Rules\Recaptcha;

class ManageThreadsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        app()->singleton(Recaptcha::class, function () {
            return \Mockery::mock(Recaptcha::class, function ($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });
    }

    private function publishThread($fields = [])
    {
        $this->signIn($this->user);

        $thread = factory(Thread::class)->make($fields);

        return $this->post('/threads', $thread->toArray());
    }

    /** @test */
    public function an_authenticated_user_can_create_a_thread()
    {
        $this->disableExceptionHandling();

        $this->signIn($this->user);

        $thread = factory(Thread::class)->make([
            'title' => 'Check this out',
            'body' => 'I rule!'
        ]);

        $response = $this->post('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token']);

        $this->get($response->headers->get('Location'))
            ->assertSee('Check this out')
            ->assertSee('I rule!')
            ->assertSee($this->user->name);
    }

    /** @test */
    public function new_users_must_first_confirm_their_email_before_creating_threads()
    {
        $user = factory('App\User')->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = factory(Thread::class)->make();

        return $this->post('/threads', $thread->toArray())
            ->assertRedirect('/threads');
    }

    /** @test */
    public function guests_may_not_create_a_thread()
    {
        $this->get("/threads/create")
            ->assertRedirect('/login');

        $this->post("/threads", [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
        factory(Channel::class, 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 3])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);
        $this->publishThread(['no-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    public function a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create(['title' => 'A Common Title']);

        $this->assertEquals($thread->fresh()->slug, 'a-common-title');

        $thread = $this->postJson('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("a-common-title-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    public function a_thread_with_a_title_that_ends_in_a_number_should_generate_a_proper_slug()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create(['title' => 'A Common Title 24']);

        $thread = $this->postJson('/threads', $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("a-common-title-24-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    public function a_thread_requires_a_title_and_body_to_be_updated()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create([
            'user_id' => auth()->id(),
        ]);

        $this->patch($thread->path(), [
            'title' => 'New title',
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'New body',
        ])->assertSessionHasErrors('title');
    }

    /** @test */
    public function unauthorized_users_may_not_update_threads()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create([
            'title' => 'Old title',
            'body' => 'Old body',
            'user_id' => factory(User::class)->create(),
        ]);

        $this->patch($thread->path(), [])->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_update_their_threads()
    {
        $this->signIn();

        $thread = factory(Thread::class)->create([
            'title' => 'Old title',
            'body' => 'Old body',
            'user_id' => auth()->id(),
        ]);

        $this->patch($thread->path(), [
            'title' => 'New title',
            'body' => 'New body',
        ]);

        $this->assertEquals($thread->fresh()->title, 'New title');
        $this->assertEquals($thread->fresh()->body, 'New body');
    }

    /** @test */
    public function authorized_users_can_deletes_threads()
    {
        $this->disableExceptionHandling();

        $this->signIn($this->user);

        $thread = factory(Thread::class)->create(['user_id' => auth()->id()]);
        $reply = factory(Reply::class)->create(['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertCount(0, Activity::all());
    }

    /** @test */
    public function unauthorized_users_may_not_delete_threads()
    {
        $thread = factory(Thread::class)->create();

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn($this->user);

        $this->delete($thread->path())->assertStatus(403);
    }
}
