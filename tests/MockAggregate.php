<?php
/**
 * This file is part of the prooph/annotations package.
 * (c) 2017 Michiel Rook <mrook@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\Annotation;

use Prooph\Common\Messaging\Command;
use Prooph\EventSourcing\AggregateChanged;

class MockAggregate extends AnnotatedAggregateRoot
{
    const AGGREGATE_ID = 'aggregateId';
    
    protected function aggregateId()
    {
        return static::AGGREGATE_ID;
    }
    
    /**
     * @CommandHandler(commandName="SomeOtherCommand")
     */
    public function __construct(Command $command)
    {
    }

    /**
     * @CommandHandler
     */
    public function doSomething(Command $command)
    {
        $this->recordThat(AggregateChanged::occur($this->aggregateId(), []));
    }

    /**
     * @EventHandler
     * @param AggregateChanged $event
     */
    public function onSomething(AggregateChanged $event)
    {
        throw new \RuntimeException($event->aggregateId());
    }

    /**
     * @CommandHandler
     */
    public function doSomethingElse(AggregateChanged $event)
    {
        $this->recordThat($event);
    }
}
