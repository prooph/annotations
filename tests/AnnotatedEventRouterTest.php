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
use Prooph\Common\Event\DefaultActionEvent;
use Prooph\Common\Messaging\Message;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;

class AnnotatedEventRouterTest extends TestCase
{
    public function testShouldAddHandlersToRoute()
    {
        $projector = new class {
            /**
             * @Prooph\Annotation\EventHandler
             * @param Message $message
             */
            public function onEvent(Message $message)
            {
            }
        };
        
        $eventRouter = new AnnotatedEventRouter($projector);
        
        $event = new DefaultActionEvent('');
        $event->setParam(MessageBus::EVENT_PARAM_MESSAGE_NAME, Message::class);
        $eventRouter->onRouteMessage($event);
        static::assertInstanceOf(EventHandlerInvoker::class, $event->getParam(EventBus::EVENT_PARAM_EVENT_LISTENERS)[0]);
    }
}
