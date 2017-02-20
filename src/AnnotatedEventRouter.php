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

use Guzzle\Service\Resource\Model;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\Common\Event\DetachAggregateHandlers;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Exception\InvalidArgumentException;
use Prooph\ServiceBus\Exception\RuntimeException;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use Prooph\ServiceBus\Plugin\Router\MessageBusRouterPlugin;

class AnnotatedEventRouter extends AbstractPlugin implements MessageBusRouterPlugin
{
    /**
     * @var ModelInspector
     */
    protected $inspector;

    /**
     * @param object $delegate
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __construct($delegate)
    {
        $this->inspector = new ModelInspector($delegate);
    }

    /**
     * Handle route action event of a message bus dispatch
     *
     * @param ActionEvent $actionEvent
     * @return void
     */
    public function onRouteMessage(ActionEvent $actionEvent): void
    {
        $messageName = (string)$actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME);

        if (empty($messageName)) {
            return;
        }
        
        $invoker = $this->inspector->getEventHandler($messageName);
        
        if ($invoker === null) {
            return;
        }

        $listeners = $actionEvent->getParam(EventBus::EVENT_PARAM_EVENT_LISTENERS, []);

        $actionEvent->setParam(EventBus::EVENT_PARAM_EVENT_LISTENERS, $listeners + [$invoker]);
    }

    /**
     * @param MessageBus $messageBus
     */
    public function attachToMessageBus(MessageBus $messageBus): void
    {
        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_DISPATCH,
            [$this, 'onRouteMessage'],
            MessageBus::PRIORITY_ROUTE
        );
    }
}
