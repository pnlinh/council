<?php
namespace Tests\Feature;

use App\User;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->thread = factory(Thread::class)->create();
        $this->reply = factory(Reply::class)->create();
    }

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $userOne = factory(User::class)->create(['name' => 'Uno']);
        $userTwo = factory(User::class)->create(['name' => 'Dos']);

        $this->signIn($userOne);

        $reply = factory(Reply::class)->make([
            'body' => 'Hi, @Dos',
            'user_id' => $userOne->id,
        ]);

        $this->json("post", "{$this->thread->path()}/replies", $reply->toArray());

        $this->assertCount(1, $userTwo->notifications);
    }

    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        factory(User::class)->create(['name' => 'johndoe']);
        factory(User::class)->create(['name' => 'johnroe']);
        factory(User::class)->create(['name' => 'janedoe']);

        $results = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $results->json());
    }
}
