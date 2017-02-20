<?php

use Prooph\Annotation\AnnotatedCommandHandler;
use Prooph\Annotation\AnnotatedCommandTargetResolver;
use Prooph\Annotation\AnnotatedEventRouter;
use Prooph\Annotation\EventSourcingRepository;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Ramsey\Uuid\Uuid;

require '../vendor/autoload.php';

require 'TodoItem.php';

$eventStore = new InMemoryEventStore();
$actionableEventStore = new ActionEventEmitterEventStore($eventStore, new ProophActionEventEmitter());

$repository = new EventSourcingRepository($actionableEventStore,
    TodoItem::class,
    null, //We don't use a snapshot store in the example
    null, //Also a custom stream name is not required
    true //But we enable the "one-stream-per-aggregate" mode
);

$commandBus = new CommandBus();

$eventBus = new EventBus();
$eventPublisher = new EventPublisher($eventBus);
$eventPublisher->attachToEventStore($actionableEventStore);

$eventRouter = new AnnotatedEventRouter(new ItemProjector());
$eventRouter->attachToMessageBus($eventBus);

$commandTargetResolver = new AnnotatedCommandTargetResolver();

$commandRouter = new AnnotatedCommandHandler(TodoItem::class, $commandTargetResolver, $repository);
$commandRouter->attachToMessageBus($commandBus);
$eventStore->beginTransaction();

$uuid = Uuid::uuid1()->toString();

$commandBus->dispatch(new PostTodo($uuid));

$eventStore->commit();
$eventStore->beginTransaction();

$commandBus->dispatch(new UpdateTodo($uuid));

$eventStore->commit();
