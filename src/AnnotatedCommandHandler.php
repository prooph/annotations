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

use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\Common\Event\DetachAggregateHandlers;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use Prooph\ServiceBus\Plugin\Router\MessageBusRouterPlugin;

class AnnotatedCommandHandler extends AbstractPlugin implements MessageBusRouterPlugin
{
    use DetachAggregateHandlers;

    /**
     * @var []
     */
    protected $handlers;

    /**
     * @var AggregateRepository
     */
    private $aggregateRepository;
    /**
     * @var CommandTargetResolver
     */
    private $commandTargetResolver;

    /**
     * AnnotatedCommandHandler constructor.
     * @param string $aggregateName
     * @param CommandTargetResolver $commandTargetResolver
     * @param AggregateRepository $aggregateRepository
     */
    public function __construct(string $aggregateName, CommandTargetResolver $commandTargetResolver, AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
        $this->commandTargetResolver = $commandTargetResolver;
        $this->handlers = $this->initializeHandlers($aggregateName);
    }

    /**
     * @param $aggregateName
     * @return array
     */
    protected function initializeHandlers($aggregateName)
    {
        $handlers = [];
        foreach (AnnotationUtils::getAnnotatedMethodsWithAttributes($aggregateName, CommandHandler::class) as list($method, $annotationAttributes)) {
            /** @var \ReflectionMethod $method */
            if ($annotationAttributes['commandName'] !== null) {
                $commandName = $annotationAttributes['commandName'];
            } else {
                $commandName = (string) $method->getParameters()[0]->getType();
            }
            
            if ($method->isConstructor()) {
                $handlers[$commandName] = new AggregateConstructorCommandHandler($aggregateName, $this->aggregateRepository);
            } else {
                $handlers[$commandName] = new AggregateCommandHandler($method, $this->commandTargetResolver, $this->aggregateRepository);
            }
        }
        return $handlers;
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

        if (!isset($this->handlers[$messageName])) {
            return;
        }
        
        $handler = $this->handlers[$messageName];

        $actionEvent->setParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER, $handler);
    }
}
