<?php

declare(strict_types=1);

namespace Fixtures\Fixtures;

use PHPUnit\Framework\TestCase;
use Fixtures\Greeting;

class GreetingTest extends TestCase
{
    public function testItGreetsUser(): void
    {
        $greeting = new Greeting('Rasmus Lerdorf');

        $this->assertSame('Hello Rasmus Lerdorf', $greeting->sayHello());
    }
}
