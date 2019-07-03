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
use Prooph\Annotation\AnnotatedAggregate;
use Prooph\Annotation\CommandTargetResolver;
use Prooph\Common\Messaging\Command;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class AggregateCommandHandlerTest extends TestCase
{
    public function testShouldInvokeTargetHandler()
    {
        $mockAggregate = new AnnotatedAggregate();
        $mockAggregate->registerAggregate(new class() {
            /**
             * @Prooph\Annotation\AggregateIdentifier
             */
            private $aggregateId;

            public function commandHandler(Command $command)
            {
                return 'commandHandled';
            }
        });

        $rm = new \ReflectionMethod(\get_class($mockAggregate->getAggregate()), 'commandHandler');

        $commandTargetResolver = $this->getMockBuilder(CommandTargetResolver::class)
            ->getMock();
        $commandTargetResolver->expects(static::once())
            ->method('resolveTarget')
            ->willReturn('100');

        $aggregateRepository = $this->getMockBuilder(AggregateRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $aggregateRepository->expects(static::once())
            ->method('getAggregateRoot')
            ->willReturn($mockAggregate);

        $handler = new AggregateCommandHandler($rm, $commandTargetResolver, $aggregateRepository);

        $result = $handler($this->getMockBuilder(Command::class)->getMock());
        static::assertEquals('commandHandled', $result);
    }
}
