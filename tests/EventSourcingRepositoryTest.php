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
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\EventStore;

class EventSourcingRepositoryTest extends TestCase
{
    public function testShouldCreateRepository()
    {
        $eventStore = $this->getMockBuilder(EventStore::class)->getMock();
        $eventStore->expects(static::once())
            ->method('create');
        
        $aggregate = $this->getMockBuilder(AnnotatedAggregate::class)->getMock();
        $aggregate->expects(static::once())
            ->method('aggregateType')
            ->willReturn(AggregateType::fromAggregateRootClass(MockAggregate::class));

        $aggregate->expects(static::once())
            ->method('getAggregateId')
            ->willReturn('aggregateId');

        $aggregate->expects(static::once())
            ->method('popRecordedEvents')
            ->willReturn([AggregateChanged::occur('aggregateId')]);

        $repository = new EventSourcingRepository($eventStore, MockAggregate::class, null, null, true);
        $repository->saveAggregateRoot($aggregate);
    }
}
