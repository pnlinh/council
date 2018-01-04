<?php
namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;

class Test extends TestCase
{
    /** @test */
    public function it_check_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply'));

        $this->expectException(\Exception::class);

        $spam->detect('yahoo customer support');
    }

    /** @test */
    public function it_checks_for_key_held_down()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('hello world'));

        $this->expectException(\Exception::class);

        $spam->detect('hello world aaaaaaaaa');
    }
}
