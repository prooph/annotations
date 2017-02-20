<?php

namespace Prooph\Annotation;

use Prooph\EventSourcing\AggregateChanged;

abstract class AggregateLifecycle
{
    /**
     * @param AggregateChanged $e
     */
    public static function recordThat(AggregateChanged $e)
    {
        $instance = AggregateManager::getInstance($e->aggregateId());
        
        if ($instance === null) {
            throw new \RuntimeException(sprintf('Did not find an aggregate root for id %s', $e->aggregateId()));
        }

        $instance->doRecordThat($e);
    }

    /**
     * @param AggregateChanged $e
     * @return mixed
     */
    protected abstract function doRecordThat(AggregateChanged $e);
}
