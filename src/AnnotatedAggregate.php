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

use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\Aggregate\AggregateTypeProvider;
use Prooph\EventSourcing\AggregateChanged;

class AnnotatedAggregate extends AggregateLifecycle implements AggregateTypeProvider
{
    /**
     * @var ModelInspector
     */
    protected $inspector;

    /**
     * @var object
     */
    protected $aggregate;

    /**
     * Current version
     *
     * @var int
     */
    protected $version = 0;

    /**
     * List of events that are not committed to the EventStore
     *
     * @var AggregateChanged[]
     */
    protected $recordedEvents = [];

    /**
     * @var AggregateChanged[]
     */
    protected $delayedEvents = [];
    
    /**
     * @param object $aggregate
     */
    public function registerAggregate($aggregate)
    {
        $this->inspector = new ModelInspector($aggregate);
        $this->aggregate = $aggregate;
        
        if (!empty($this->delayedEvents)) {
            foreach ($this->delayedEvents as $event) {
                $this->apply($event);
            }
            
            $this->delayedEvents = [];
        }
    }

    /**
     * @param AggregateChanged $e
     * @throws \RuntimeException
     */
    protected function apply(AggregateChanged $e)
    {
        if ($this->aggregate !== null) {
            $invoker = $this->inspector->getEventHandler(get_class($e));
            if ($invoker !== null) {
                $invoker($e);
            }
        } else {
            $this->delayedEvents[] = $e;
        }
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }
    
    /**
     * @return string representation of the unique identifier of the aggregate root
     */
    public function getAggregateId()
    {
        $properties = AnnotationUtils::getAnnotatedProperties(get_class($this->aggregate), AggregateIdentifier::class);
        
        if (empty($properties)) {
            throw new \RuntimeException(sprintf('Missing AggregateIdentifier annotation on aggregate root %s', get_class($this->aggregate)));
        }
        
        $property = reset($properties);
        $property->setAccessible(true);
        return $property->getValue($this->aggregate);
    }

    /**
     * @param AggregateChanged $event
     * @return void
     */
    protected function doRecordThat(AggregateChanged $event)
    {
        $this->version += 1;

        $this->recordedEvents[] = $event->withVersion($this->version);

        $this->apply($event);
    }

    public function aggregateType(): AggregateType
    {
        return AggregateType::fromAggregateRoot($this->aggregate);
    }

    /**
     * Get pending events and reset stack
     *
     * @return AggregateChanged[]
     */
    public function popRecordedEvents(): array
    {
        $pendingEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $pendingEvents;
    }

    /**
     * Replay past events
     *
     * @throws \RuntimeException
     */
    public function replay(\Iterator $historyEvents): void
    {
        foreach ($historyEvents as $pastEvent) {
            /** @var AggregateChanged $pastEvent */
            $this->version = $pastEvent->version();

            $this->apply($pastEvent);
        }
    }

    /**
     * @return object
     */
    public function getAggregate()
    {
        return $this->aggregate;
    }

}
