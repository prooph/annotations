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

namespace Prooph\Annotation;

use Prooph\Common\Messaging\Command;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class AggregateConstructorCommandHandler
{
    /**
     * @var EventSourcingRepository
     */
    private $aggregateRepository;

    /**
     * @var \ReflectionMethod
     */
    private $method;

    /**
     * AggregateConstructorCommandHandler constructor.
     * @param AggregateRepository $aggregateRepository
     */
    public function __construct(\ReflectionMethod $method, EventSourcingRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->method = $method;
    }

    /**
     * @param Command $message
     * @return object
     */
    public function __invoke(Command $message)
    {
        $instance = AggregateManager::newInstance(function () use ($message) {
            return $this->method->getDeclaringClass()->newInstance($message);
        });
        $this->aggregateRepository->saveAggregateRoot($instance);

        return $instance;
    }
}
