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
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class AggregateConstructorCommandHandlerTest extends TestCase
{
    public function testShouldInvokeTargetHandler()
    {
        $aggregateRepository = $this->getMockBuilder(AggregateRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $aggregateRepository->expects(static::once())
            ->method('saveAggregateRoot');

        $handler = new AggregateConstructorCommandHandler(MockHandler::class, $aggregateRepository);
        
        $result = $handler($this->getMockBuilder(Command::class)->getMock());
        static::assertInstanceOf(MockHandler::class, $result);
    }
}

class MockHandler
{
    public function __construct(Command $command)
    {
    }
}
