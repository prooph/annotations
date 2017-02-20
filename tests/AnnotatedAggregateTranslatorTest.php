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
use Prooph\EventSourcing\Aggregate\AggregateType;

class AnnotatedAggregateTranslatorTest extends TestCase
{
    /**
     * @var AnnotatedAggregateTranslator
     */
    protected $translator;

    protected function setUp(): void
    {
        $this->translator = new AnnotatedAggregateTranslator();
    }

    public function testShouldExtractAggregateVersion()
    {
        $mockAggregate = $this->getMockBuilder(AnnotatedAggregate::class)->getMock();
        $mockAggregate->expects(static::once())
            ->method('getVersion')
            ->willReturn(101);
        
        static::assertEquals(101, $this->translator->extractAggregateVersion($mockAggregate));
    }

    public function testShouldExtractAggregateId()
    {
        $mockAggregate = $this->getMockBuilder(AnnotatedAggregate::class)->getMock();
        $mockAggregate->expects(static::once())
            ->method('getAggregateId')
            ->willReturn('aggregateId');
        
        static::assertEquals('aggregateId', $this->translator->extractAggregateId($mockAggregate));
    }

    public function testShouldReconstituteFromHistory()
    {
        $type = AggregateType::fromAggregateRootClass(MockAggregate::class);
        $result = $this->translator->reconstituteAggregateFromHistory($type, new \ArrayIterator());
        static::assertInstanceOf(AnnotatedAggregate::class, $result);
    }

    public function testShouldExtractPendingStreamEvents()
    {
        $mockAggregate = $this->getMockBuilder(AnnotatedAggregate::class)->getMock();
        $mockAggregate->expects(static::once())
            ->method('popRecordedEvents')
            ->willReturn([]);

        static::assertEquals([], $this->translator->extractPendingStreamEvents($mockAggregate));
    }

    public function testShoulReplayStreamEvents()
    {
        $eventsIterator = new \ArrayIterator();
        
        $mockAggregate = $this->getMockBuilder(AnnotatedAggregate::class)->getMock();
        $mockAggregate->expects(static::once())
            ->method('replay')
            ->with($eventsIterator)
            ->willReturn([]);

        $this->translator->replayStreamEvents($mockAggregate, $eventsIterator);
    }
}
