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

namespace Prooph\Annotation;

use Iterator;
use Prooph\Common\Messaging\Message;
use Prooph\EventSourcing\Aggregate\AggregateTranslator as EventStoreAggregateTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;

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
