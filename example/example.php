<?php

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Adapter\InMemoryAdapter;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;

require '../vendor/autoload.php';

require 'TodoItem.php';

$adapter = new InMemoryAdapter();
$eventStore = new EventStore($adapter, new ProophActionEventEmitter());

$repository = new AggregateRepository($eventStore,
    AggregateType::fromAggregateRootClass(TodoItem::class),
    new AggregateTranslator(),
    null, //We don't use a snapshot store in the example
    null, //Also a custom stream name is not required
    true //But we enable the "one-stream-per-aggregate" mode
);

$commandBus = new \Prooph\ServiceBus\CommandBus();

$eventBus = new \Prooph\ServiceBus\EventBus();
$eventPublisher = new \Prooph\EventStoreBusBridge\EventPublisher($eventBus);
$eventPublisher->setUp($eventStore);

$eventRouter = new \Prooph\Annotation\AnnotatedEventRouter(new ItemProjector());
$eventRouter->attach($eventBus->getActionEventEmitter());

$commandTargetResolver = new \Prooph\Annotation\AnnotatedCommandTargetResolver();

$commandRouter = new \Prooph\Annotation\AnnotatedCommandHandler(TodoItem::class, $commandTargetResolver, $repository);
$commandRouter->attach($commandBus->getActionEventEmitter());

$eventStore->beginTransaction();

$uuid = \Rhumsaa\Uuid\Uuid::uuid1()->toString();

$commandBus->dispatch(new PostTodo($uuid));

$eventStore->commit();
$eventStore->beginTransaction();

$commandBus->dispatch(new UpdateTodo($uuid));

$eventStore->commit();