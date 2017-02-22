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

use Prooph\Common\Messaging\Message;

class AnnotatedHandlerInvoker
{
    /**
     * @var object
     */
    private $delegate;

    /**
     * @var \ReflectionMethod
     */
    private $handler;

    /**
     * EventHandlerInvoker constructor.
     * @param object $delegate
     * @param \ReflectionMethod $target
     */
    public function __construct($delegate, \ReflectionMethod $target)
    {
        $this->delegate = $delegate;
        $this->handler = $target;
    }

    /**
     * @param Message $event
     * @return mixed
     */
    public function __invoke(Message $event)
    {
        $this->handler->setAccessible(true);

        return $this->handler->invoke($this->delegate, $event);
    }
}
