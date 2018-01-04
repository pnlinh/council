<?php

namespace Tests\Unit;

use App\Reply;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_reply_has_an_owner()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test */
    public function a_reply_belongs_to_a_thread()
    {
        $reply = factory(Reply::class)->create();

        $this->assertInstanceOf('App\Thread', $reply->thread);
    }

    /** @test */
    public function it_knows_if_it_was_just_published()
    {
        $reply = factory(Reply::class)->create();

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subWeek();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function it_can_detect_all_mentioned_user_in_body()
    {
        $reply = new Reply([
            'body' => '@Foo wants to meet @Bar'
        ]);

        $this->assertEquals(['Foo','Bar'], $reply->mentionedUsers());
    }

    /** @test */
    public function it_wraps_mentioned_usernames_in_the_body_within_anchor_tags()
    {
        $reply = new Reply([
            'body' => 'Hello @Clarice'
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/Clarice">@Clarice</a>',
            $reply->body
        );
    }

    /** @test */
    public function it_knows_if_it_is_the_best_reply()
    {
        $reply = factory(Reply::class)->create();

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());
    }


    /** @test */
    public function a_relys_body_is_sanitized_automatically()
    {
        $thread = factory(Reply::class)->make(['body' => '<script>alert("gotcha");</script>']);

        $this->assertEmpty($thread->body);
    }
}
