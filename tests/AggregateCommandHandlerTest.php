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

        $rm = new \ReflectionMethod(get_class($mockAggregate->getAggregate()), 'commandHandler');

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
