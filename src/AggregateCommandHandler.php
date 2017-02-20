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
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class AggregateCommandHandler
{
    /**
     * @var AggregateRepository
     */
    private $aggregateRepository;

    /**
     * @var \ReflectionMethod
     */
    private $handler;

    /**
     * @var CommandTargetResolver
     */
    private $commandTargetResolver;

    /**
     * AggregateConstructorCommandHandler constructor.
     * @param \ReflectionMethod $handler
     * @param CommandTargetResolver $commandTargetResolver
     * @param AggregateRepository $aggregateRepository
     */
    public function __construct(\ReflectionMethod $handler, CommandTargetResolver $commandTargetResolver, AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->handler = $handler;
        $this->commandTargetResolver = $commandTargetResolver;
    }

    /**
     * @param Command $message
     * @return mixed
     */
    public function __invoke(Command $message)
    {
        $aggregateIdentifier = $this->commandTargetResolver->resolveTarget($message);
        $aggregate = $this->aggregateRepository->getAggregateRoot($aggregateIdentifier);
        
        $this->handler->setAccessible(true);
        return $this->handler->invoke($aggregate->getAggregate(), $message);
    }
}
