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
use Prooph\EventStore\Aggregate\AggregateRepository;

class AggregateConstructorCommandHandler
{
    /**
     * @var AggregateRepository
     */
    private $aggregateRepository;

    /**
     * @var string
     */
    private $aggregateName;

    /**
     * AggregateConstructorCommandHandler constructor.
     * @param string $aggregateName
     * @param AggregateRepository $aggregateRepository
     */
    public function __construct(string $aggregateName, AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->aggregateName = $aggregateName;
    }

    /**
     * @param Command $message
     * @return object
     */
    public function __invoke(Command $message)
    {
        $ref = new \ReflectionClass($this->aggregateName);
        $instance = $ref->newInstanceArgs([$message]);
        $this->aggregateRepository->addAggregateRoot($instance);
        return $instance;
    }
}
