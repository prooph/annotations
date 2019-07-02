<?php

/**
 * This file is part of prooph/annotations.
 * (c) 2017-2019 Michiel Rook <mrook@php.net>
 * (c) 2017-2019 prooph Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2017-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\Annotation;

use PHPUnit\Framework\TestCase;
use Prooph\Annotation\AggregateCommandHandler;
use Prooph\Annotation\AnnotatedCommandHandler;
use Prooph\Annotation\CommandTargetResolver;
use Prooph\Annotation\EventSourcingRepository;
use Prooph\Common\Event\DefaultActionEvent;
use Prooph\Common\Event\ListenerHandler;
use Prooph\Common\Messaging\Command;
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

        $aggregateRepository = $this->getMockBuilder(EventSourcingRepository::class)
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
        $emitter = $this->getMockBuilder(MessageBus::class)->getMock();

        $emitter->expects(static::once())
            ->method('attach')
            ->willReturn($this->getMockBuilder(ListenerHandler::class)->getMock());

        $this->handler->attachToMessageBus($emitter);
    }
}
