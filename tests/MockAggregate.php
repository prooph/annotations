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

use Prooph\Common\Messaging\Command;
use Prooph\EventSourcing\AggregateChanged;

class MockAggregate
{
    const AGGREGATE_ID = 'aggregateId';

    /**
     * @var string
     * @AggregateIdentifier
     */
    private $aggregateId = self::AGGREGATE_ID;

    /**
     * @CommandHandler(commandName="SomeOtherCommand")
     */
    public function __construct(Command $command)
    {
        AggregateLifecycle::recordThat(AggregateChanged::occur($this->aggregateId, []));
    }

    /**
     * @CommandHandler
     */
    public function doSomething(Command $command)
    {
        AggregateLifecycle::recordThat(AggregateChanged::occur($this->aggregateId, []));
    }

    /**
     * @EventHandler
     * @param AggregateChanged $event
     */
    public function onSomething(AggregateChanged $event)
    {
        //        throw new \RuntimeException($event->aggregateId());
    }

    /**
     * @CommandHandler
     */
    public function doSomethingElse(AggregateChanged $event)
    {
        AggregateLifecycle::recordThat($event);
    }
}
