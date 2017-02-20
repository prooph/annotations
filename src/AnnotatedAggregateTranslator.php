<?php

namespace Prooph\Annotation;

use Iterator;
use Prooph\Common\Messaging\Message;
use Prooph\EventSourcing\Aggregate\AggregateTranslator as EventStoreAggregateTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\Aggregate\Exception\RuntimeException;

class AnnotatedAggregateTranslator implements EventStoreAggregateTranslator
{

    /**
     * @param object $eventSourcedAggregateRoot
     * @return int
     */
    public function extractAggregateVersion($eventSourcedAggregateRoot): int
    {
        return $eventSourcedAggregateRoot->getVersion();
    }

    /**
     * @param object $eventSourcedAggregateRoot
     * @return string
     */
    public function extractAggregateId($eventSourcedAggregateRoot): string
    {
        return $eventSourcedAggregateRoot->getAggregateId();
    }

    /**
     * @return object reconstructed EventSourcedAggregateRoot
     */
    public function reconstituteAggregateFromHistory(AggregateType $aggregateType, Iterator $historyEvents)
    {
        $wrapper = new AnnotatedAggregate();
        $rc = new \ReflectionClass($aggregateType->toString());
        $aggregate = $rc->newInstanceWithoutConstructor();
        $wrapper->registerAggregate($aggregate);
        $wrapper->replay($historyEvents);
        return $wrapper;
    }

    /**
     * @param object $eventSourcedAggregateRoot
     *
     * @return Message[]
     */
    public function extractPendingStreamEvents($eventSourcedAggregateRoot): array
    {
        return $eventSourcedAggregateRoot->popRecordedEvents();
    }

    /**
     * @param object $eventSourcedAggregateRoot
     * @param Iterator $events
     */
    public function replayStreamEvents($eventSourcedAggregateRoot, Iterator $events): void
    {
        $eventSourcedAggregateRoot->replay($events);
    }
}
