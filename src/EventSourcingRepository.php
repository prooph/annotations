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
