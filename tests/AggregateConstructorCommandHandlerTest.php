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
use Prooph\Common\Messaging\Command;

class AggregateConstructorCommandHandlerTest extends TestCase
{
    public function testShouldInvokeTargetHandler()
    {
        $aggregateRepository = $this->getMockBuilder(EventSourcingRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $aggregateRepository->expects(static::once())
            ->method('saveAggregateRoot');

        $rc = new \ReflectionClass(MockAggregate::class);
        $handler = new AggregateConstructorCommandHandler($rc->getConstructor(), $aggregateRepository);
        
        $result = $handler($this->getMockBuilder(Command::class)->getMock());
        static::assertInstanceOf(AnnotatedAggregate::class, $result);
        static::assertInstanceOf(MockAggregate::class, $result->getAggregate());
    }
}
