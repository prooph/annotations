<?php
/**
 * This file is part of the prooph/annotations package.
 * (c) 2017 Michiel Rook <mrook@php.net>
 * (c) 2017 prooph software GmbH <contact@prooph.de>
 * (c) 2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\Annotation;

class AggregateManager
{
    /**
     * @var AnnotatedAggregate[]
     */
    protected static $managedAggregates = [];

    /**
     * @var AnnotatedAggregate
     */
    protected static $lastCreatedAggregate;

    /**
     * Resets the Aggregate Manager
     */
    public static function reset()
    {
        static::$managedAggregates = [];
        static::$lastCreatedAggregate = null;
    }

    /**
     * @param callable $factoryMethod
     * @return AnnotatedAggregate
     * @throws \RuntimeException
     */
    public static function newInstance(callable $factoryMethod): AnnotatedAggregate
    {
        $wrapper = new AnnotatedAggregate();
        static::$lastCreatedAggregate = $wrapper;

        $wrapper->registerAggregate($factoryMethod());

        $aggregateId = $wrapper->getAggregateId();

        if (isset(static::$managedAggregates[$aggregateId])) {
            throw new \RuntimeException(sprintf('Already have a managed aggregate for aggregate id %s', $aggregateId));
        }

        static::$managedAggregates[$aggregateId] = $wrapper;

        return $wrapper;
    }

    /**
     * @param string $aggregateId
     * @return null|AnnotatedAggregate
     */
    public static function getInstance(string $aggregateId): ?AnnotatedAggregate
    {
        if (isset(static::$managedAggregates[$aggregateId])) {
            return static::$managedAggregates[$aggregateId];
        }

        return static::$lastCreatedAggregate;
    }
}
