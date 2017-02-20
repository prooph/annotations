<?php

namespace Prooph\Annotation;

use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;

class EventSourcingRepository extends AggregateRepository
{
    public function __construct(
        EventStore $eventStore,
        string $aggregateRoot,
        SnapshotStore $snapshotStore = null,
        StreamName $streamName = null,
        $oneStreamPerAggregate = false
    ) {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass($aggregateRoot),
            new AnnotatedAggregateTranslator(),
            $snapshotStore,
            $streamName,
            $oneStreamPerAggregate
        );
    }
}
