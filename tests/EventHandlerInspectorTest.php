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

class EventHandlerInspectorTest extends TestCase
{
    public function testShouldFindHandlerByMessageType()
    {
        $delegate = new class {
            /**
             * @Prooph\Annotation\EventHandler
             * @param Message $message
             */
            public function handle(Message $message)
            {
            }
        };
        
        $inspector = new EventHandlerInspector($delegate);
        static::assertInstanceOf(EventHandlerInvoker::class, $inspector->findMessageInvoker(Message::class));
    }

    public function testShouldFindHandlerByMessageName()
    {
        $delegate = new class {
            /**
             * @Prooph\Annotation\EventHandler(eventName="SomeMessage")
             * @param Message $message
             */
            public function handle(object $message)
            {
            }
        };
        
        $inspector = new EventHandlerInspector($delegate);
        static::assertInstanceOf(EventHandlerInvoker::class, $inspector->findMessageInvoker('SomeMessage'));
    }

    public function testShouldNotFindHandlerForUnknownMessage()
    {
        $delegate = new class {
            /**
             * @Prooph\Annotation\EventHandler(eventName="SomeMessage")
             * @param Message $message
             */
            public function handle(object $message)
            {
            }
        };
        
        $inspector = new EventHandlerInspector($delegate);
        static::assertNull($inspector->findMessageInvoker('AnotherMessage'));
    }
}
