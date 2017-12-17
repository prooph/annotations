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

class ModelInspector
{
    /**
     * @var AnnotatedHandlerInvoker[]
     */
    protected $commandHandlers = [];

    /**
     * @var AnnotatedHandlerInvoker[]
     */
    protected $eventHandlers = [];

    public function __construct($delegate)
    {
        $this->initializeHandlers($delegate);
    }

    /**
     * @param object $delegate
     */
    protected function initializeHandlers($delegate)
    {
        $className = null === $delegate ? get_class() : get_class($delegate);
        foreach (AnnotationUtils::getAnnotatedMethodsWithAttributes($className, CommandHandler::class) as list($method, $annotationAttributes)) {
            /** @var \ReflectionMethod $method */
            if ($annotationAttributes['commandName'] !== null) {
                $commandName = $annotationAttributes['commandName'];
            } else {
                $commandName = (string) $method->getParameters()[0]->getType();
            }
            $this->commandHandlers[$commandName] = new AnnotatedHandlerInvoker($delegate, $method);
        }

        foreach (AnnotationUtils::getAnnotatedMethodsWithAttributes($className, EventHandler::class) as list($method, $annotationAttributes)) {
            /** @var \ReflectionMethod $method */
            if ($annotationAttributes['eventName'] !== null) {
                $eventName = $annotationAttributes['eventName'];
            } else {
                $eventName = (string) $method->getParameters()[0]->getType();
            }
            $this->eventHandlers[$eventName] = new AnnotatedHandlerInvoker($delegate, $method);
        }
    }

    /**
     * @param string $commandName
     * @return null|AnnotatedHandlerInvoker
     */
    public function getCommandHandler(string $commandName): ?AnnotatedHandlerInvoker
    {
        if (! isset($this->commandHandlers[$commandName])) {
            return null;
        }

        return $this->commandHandlers[$commandName];
    }

    /**
     * @param string $eventName
     * @return null|AnnotatedHandlerInvoker
     */
    public function getEventHandler(string $eventName): ?AnnotatedHandlerInvoker
    {
        if (! isset($this->eventHandlers[$eventName])) {
            return null;
        }

        return $this->eventHandlers[$eventName];
    }
}
