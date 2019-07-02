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
            throw new \RuntimeException(\sprintf('Did not find an aggregate root for id %s', $e->aggregateId()));
        }

        $instance->doRecordThat($e);
    }

    /**
     * @param AggregateChanged $e
     * @return mixed
     */
    abstract protected function doRecordThat(AggregateChanged $e);
}
