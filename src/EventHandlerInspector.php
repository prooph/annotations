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

class EventHandlerInspector
{
    /**
     * @var EventHandlerInvoker[]
     */
    protected $eventMap;

    public function __construct($delegate)
    {
        $this->initializeHandlers($delegate);
    }

    /**
     * @param object $delegate
     */
    protected function initializeHandlers($delegate)
    {
        foreach (AnnotationUtils::getAnnotatedMethodsWithAttributes(get_class($delegate), EventHandler::class) as list($method, $annotationAttributes)) {
            /** @var \ReflectionMethod $method */
            if ($annotationAttributes['eventName'] !== null) {
                $eventName = $annotationAttributes['eventName'];
            } else {
                $eventName = (string) $method->getParameters()[0]->getType();
            }
            $this->eventMap[$eventName] = new EventHandlerInvoker($delegate, $method);
        }
    }

    /**
     * @param string $eventName
     * @return null|EventHandlerInvoker
     */
    public function findMessageInvoker(string $eventName)
    {
        if (!isset($this->eventMap[$eventName])) {
            return null;
        }

        return $this->eventMap[$eventName];
    }
}
