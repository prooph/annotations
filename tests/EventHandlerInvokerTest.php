<?php
/**
 * This file is part of the prooph/annotations package.
 * (c) 2017 Michiel Rook <mrook@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\Annotation;

use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Message;

class EventHandlerInvokerTest extends TestCase
{
    public function testShouldInvokeTarget()
    {
        $delegate = new class {
            public function handle(Message $message)
            {
                return 'handler';
            }
        };
        
        $invoker = new EventHandlerInvoker($delegate, new \ReflectionMethod($delegate, 'handle'));
        static::assertEquals('handler', $invoker($this->getMockBuilder(Message::class)->getMock()));
    }
}
