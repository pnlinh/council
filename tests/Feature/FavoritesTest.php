<?php

use App\User;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritesTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->reply = factory(Reply::class)->create();
        $this->thread = factory(Thread::class)->create();
    }

    /** @test */
    public function guests_cannot_favorite_anything()
    {
        $this->post("/replies/{$this->reply->id}/favorites")
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->disableExceptionHandling();

        $this->signIn($this->user);

        $this->post("/replies/{$this->reply->id}/favorites");

        $this->assertCount(1, $this->reply->favorites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->disableExceptionHandling();

        $this->signIn($this->user);

        $this->post("/replies/{$this->reply->id}/favorites");
        $this->assertCount(1, $this->reply->favorites);

        $this->delete("/replies/{$this->reply->id}/favorites");
        $this->assertCount(0, $this->reply->fresh()->favorites);
    }

    /** @test */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->disableExceptionHandling();

        $this->signIn($this->user);

        $this->post("/replies/{$this->reply->id}/favorites");
        $this->post("/replies/{$this->reply->id}/favorites");

        $this->assertCount(1, $this->reply->favorites);
    }

    /** @test */
    public function a_reply_can_check_the_favorited_status()
    {
        $this->signIn($this->user);

        $this->assertFalse($this->reply->isFavorited());

        $this->post("/replies/{$this->reply->id}/favorites");

        $this->assertTrue($this->reply->fresh()->isFavorited());
    }
}
