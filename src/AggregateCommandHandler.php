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

class AggregateCommandHandler
{
    /**
     * @var AggregateRepository
     */
    private $aggregateRepository;

    /**
     * @var string
     */
    private $target;

    /**
     * @var CommandTargetResolver
     */
    private $commandTargetResolver;

    /**
     * AggregateConstructorCommandHandler constructor.
     * @param \ReflectionMethod $target
     * @param CommandTargetResolver $commandTargetResolver
     * @param AggregateRepository $aggregateRepository
     */
    public function __construct(\ReflectionMethod $target, CommandTargetResolver $commandTargetResolver, AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->target = $target;
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
        
        $this->target->setAccessible(true);
        return $this->target->invoke($aggregate, $message);
    }
}
