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
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\DefaultActionEvent;
use Prooph\Common\Event\ListenerHandler;
use Prooph\Common\Messaging\Command;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\ServiceBus\MessageBus;

class AnnotatedCommandHandlerTest extends TestCase
{
    /**
     * @var AnnotatedCommandHandler
     */
    protected $handler;

    protected function setUp()
    {
        $commandTargetResolver = $this->getMockBuilder(CommandTargetResolver::class)
            ->getMock();

        $aggregateRepository = $this->getMockBuilder(AggregateRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = new AnnotatedCommandHandler(
            MockAggregate::class, $commandTargetResolver, $aggregateRepository
        );
    }

    public function testShouldRouteKnownCommandToHandler()
    {
        $event = new DefaultActionEvent('event');
        $event->setParam(MessageBus::EVENT_PARAM_MESSAGE_NAME, Command::class);
        $this->handler->onRouteMessage($event);
        static::assertInstanceOf(AggregateCommandHandler::class, $event->getParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER));
    }

    public function testShouldNotRouteUnknownCommand()
    {
        $event = new DefaultActionEvent('event');
        $event->setParam(MessageBus::EVENT_PARAM_MESSAGE_NAME, '');
        $this->handler->onRouteMessage($event);
        static::assertEmpty($event->getParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER));
    }

    public function testShouldNotRouteUnknownHandler()
    {
        $event = new DefaultActionEvent('event');
        $event->setParam(MessageBus::EVENT_PARAM_MESSAGE_NAME, 'AnotherCommand');
        $this->handler->onRouteMessage($event);
        static::assertEmpty($event->getParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER));
    }
    
    public function testShouldAttachToEmitter()
    {
        $emitter = $this->getMockBuilder(ActionEventEmitter::class)->getMock();
        
        $emitter->expects(static::once())
            ->method('attachListener')
            ->willReturn($this->getMockBuilder(ListenerHandler::class)->getMock());
        
        $this->handler->attach($emitter);
    }
}
