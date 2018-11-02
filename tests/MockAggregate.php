<?php

/**
 * This file is part of prooph/annotations.
 * (c) 2017-2018 Michiel Rook <mrook@php.net>
 * (c) 2017-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2017-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\Annotation;

use Prooph\Annotation\AggregateIdentifier;
use Prooph\Annotation\AggregateLifecycle;
use Prooph\Annotation\CommandHandler;
use Prooph\Annotation\EventHandler;
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
