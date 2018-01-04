<?php

namespace Tests\Unit;

use App\Channel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ChannelTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->channel = factory(Channel::class)->create();
    }

    /** @test */
    public function a_channel_has_threads()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->channel->threads);
    }
}
