<?php
/**
 * This file is part of the prooph/annotations package.
 * (c) 2017 Michiel Rook <mrook@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Prooph\Annotation;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

abstract class AnnotatedAggregateRoot extends AggregateRoot
{
    /**
     * @var array[\ReflectionMethod[]]
     */
    protected $eventMap = [];

    protected function __construct()
    {
        parent::__construct();
        
        $this->initializeHandlers();
    }

    private function initializeHandlers()
    {
        foreach (AnnotationUtils::getAnnotatedMethodsWithAttributes(static::class, EventHandler::class) as list($method, $annotationAttributes)) {
            /** @var \ReflectionMethod $method */
            if ($annotationAttributes['eventName'] !== null) {
                $eventName = $annotationAttributes['eventName'];
            } else {
                $eventName = (string) $method->getParameters()[0]->getType();
            }
            $this->eventMap[$eventName] = $method;
        }
    }

    protected function apply(AggregateChanged $e)
    {
        $eventClass = get_class($e);
        if (!isset($this->eventMap[$eventClass])) {
            throw new \RuntimeException(sprintf(
                "Missing event handler for event %s on aggregate root %s",
                $eventClass,
                get_class($this)
            ));
        }
        
        foreach ($this->eventMap[$eventClass] as $method) {
            /** @var \ReflectionMethod $method */
            $method->setAccessible(true);
            $method->invoke($this, $e);
        }
    }
}
