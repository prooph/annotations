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

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

abstract class AnnotatedAggregateRoot extends AggregateRoot
{
    /**
     * @var EventHandlerInspector
     */
    protected $inspector;

    /**
     * @param \Iterator $historyEvents
     * @return static
     */
    protected static function reconstituteFromHistory(\Iterator $historyEvents): AggregateRoot
    {
        $rc = new \ReflectionClass(static::class);
        $instance = $rc->newInstanceWithoutConstructor();
        $instance->replay($historyEvents);

        return $instance;
    }

    /**
     * @param AggregateChanged $e
     * @throws \RuntimeException
     */
    protected function apply(AggregateChanged $e): void
    {
        $this->ensureInspectorInitialized();
        
        $invoker = $this->inspector->findMessageInvoker(get_class($e));
        if ($invoker === null) {
            throw new \RuntimeException(sprintf(
                "Missing event handler for event %s on aggregate root %s",
                get_class($e),
                get_class($this)
            ));
        }
        
        $invoker($e);
    }

    protected function ensureInspectorInitialized()
    {
        if ($this->inspector === null) {
            $this->inspector = new EventHandlerInspector($this);
        }
    }
}
