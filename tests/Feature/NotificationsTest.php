<?php
namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->thread = factory(Thread::class)->create();
        $this->user = factory(User::class)->create();
        $this->signIn($this->user);
        $this->thread->subscribe();
    }

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_from_another_user()
    {
        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $this->thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'A reply.',
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $this->thread->addReply([
            'user_id' => factory(User::class)->create()->id,
            'body' => 'A reply.',
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test **/
    public function a_user_can_fetch_their_unread_notifications()
    {
        factory(DatabaseNotification::class)->create();

        $this->assertCount(
            1,
            $this->getJson("/profiles/{$this->user->name}/notifications")->json()
        );
    }

    /** @test */
    public function a_user_can_mark_a_notification_as_read()
    {
        factory(DatabaseNotification::class)->create();

        $this->assertCount(1, $this->user->fresh()->unreadNotifications);

        $notificationId = $this->user->unreadNotifications->first()->id;

        $this->delete("/profiles/{$this->user->name}/notifications/{$notificationId}");

        $this->assertCount(0, $this->user->fresh()->unreadNotifications);
    }
}
