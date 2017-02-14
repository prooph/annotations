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

use Prooph\ServiceBus\Exception\InvalidArgumentException;
use Prooph\ServiceBus\Exception\RuntimeException;
use Prooph\ServiceBus\Plugin\Router\EventRouter;

class AnnotatedEventRouter extends EventRouter
{
    /**
     * @param object $delegate
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __construct($delegate)
    {
        parent::__construct([]);
        
        $this->initializeHandlers($delegate);
    }

    /**
     * @param object $delegate
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
            $this->route($eventName)->to(new EventHandlerInvoker($delegate, $method));
        }
    }
}
