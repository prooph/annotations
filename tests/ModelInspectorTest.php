<?php
/**
 * This file is part of the prooph/annotations package.
 * (c) 2017 Michiel Rook <mrook@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\Annotation;

use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Message;

class ModelInspectorTest extends TestCase
{
    public function testShouldFindCommandHandlerByMessageType()
    {
        $delegate = new class() {
            /**
             * @Prooph\Annotation\CommandHandler
             * @param Message $message
             */
            public function handle(Message $message)
            {
            }
        };

        $inspector = new ModelInspector($delegate);
        static::assertInstanceOf(AnnotatedHandlerInvoker::class, $inspector->getCommandHandler(Message::class));
    }

    public function testShouldFindCommandHandlerByMessageName()
    {
        $delegate = new class() {
            /**
             * @Prooph\Annotation\CommandHandler(commandName="SomeMessage")
             * @param Message $message
             */
            public function handle(Message $message)
            {
            }
        };

        $inspector = new ModelInspector($delegate);
        static::assertInstanceOf(AnnotatedHandlerInvoker::class, $inspector->getCommandHandler('SomeMessage'));
    }

    public function testShouldNotFindCommandHandlerForUnknownMessage()
    {
        $delegate = new class() {
            /**
             * @Prooph\Annotation\CommandHandler(commandName="SomeMessage")
             * @param Message $message
             */
            public function handle(Message $message)
            {
            }
        };

        $inspector = new ModelInspector($delegate);
        static::assertNull($inspector->getCommandHandler('AnotherMessage'));
    }

    public function testShouldFindEventHandlerByMessageType()
    {
        $delegate = new class() {
            /**
             * @Prooph\Annotation\EventHandler
             * @param Message $message
             */
            public function handle(Message $message)
            {
            }
        };

        $inspector = new ModelInspector($delegate);
        static::assertInstanceOf(AnnotatedHandlerInvoker::class, $inspector->getEventHandler(Message::class));
    }

    public function testShouldFindEventHandlerByMessageName()
    {
        $delegate = new class() {
            /**
             * @Prooph\Annotation\EventHandler(eventName="SomeMessage")
             * @param Message $message
             */
            public function handle(Message $message)
            {
            }
        };

        $inspector = new ModelInspector($delegate);
        static::assertInstanceOf(AnnotatedHandlerInvoker::class, $inspector->getEventHandler('SomeMessage'));
    }

    public function testShouldNotFindEventHandlerForUnknownMessage()
    {
        $delegate = new class() {
            /**
             * @Prooph\Annotation\EventHandler(eventName="SomeMessage")
             * @param Message $message
             */
            public function handle(Message $message)
            {
            }
        };

        $inspector = new ModelInspector($delegate);
        static::assertNull($inspector->getEventHandler('AnotherMessage'));
    }
}
