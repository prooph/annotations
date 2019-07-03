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
