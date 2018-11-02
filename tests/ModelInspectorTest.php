<?php

/**
 * This file is part of prooph/annotations.
 * (c) 2017-2018 Michiel Rook <mrook@php.net>
 * (c) 2017-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2017-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\Annotation;

use PHPUnit\Framework\TestCase;
use Prooph\Annotation\AnnotatedHandlerInvoker;
use Prooph\Annotation\ModelInspector;
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
