<?php

use Prooph\Annotation\AggregateIdentifier;
use Prooph\Annotation\AggregateLifecycle;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\Message;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\Annotation\EventHandler;
use Prooph\Annotation\CommandHandler;
use Prooph\Annotation\TargetAggregateIdentifier;

class PostTodo extends Command
{
    /**
     * @var string
     */
    protected $itemId;

    public function __construct($itemId)
    {
        $this->itemId = $itemId;
        $this->init();
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Return message payload as array
     *
     * The payload should only contain scalar types and sub arrays.
     * The payload is normally passed to json_encode to persist the message or
     * push it into a message queue.
     *
     * @return array
     */
    public function payload(): array
    {
        return ['itemId' => $this->itemId];
    }

    /**
     * This method is called when message is instantiated named constructor fromArray
     *
     * @param array $payload
     * @return void
     */
    protected function setPayload(array $payload): void
    {
        $this->itemId = $payload['itemId'];
    }
}

class UpdateTodo extends PostTodo
{
    /**
     * @TargetAggregateIdentifier
     */
    protected $itemId;
}

class TodoPosted extends AggregateChanged
{
    /**
     * @var string
     */
    private $itemId;

    public function __construct($itemId)
    {
        parent::__construct($itemId, []);
        $this->itemId = $itemId;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    public function withMetadata(array $metadata): Message
    {
        $event = clone $this;
        $event->metadata = $metadata;

        return $event;
    }

    public function withAddedMetadata(string $key, $value): Message
    {
        $event = clone $this;
        $event->metadata[$key] = $value;

        return $event;
    }

    public function withVersion(int $version): AggregateChanged
    {
        $event = clone $this;
        $event->version = $version;

        return $event;
    }
}

class TodoUpdated extends AggregateChanged
{
    /**
     * @var string
     */
    private $itemId;

    public function __construct($itemId)
    {
        parent::__construct($itemId, []);
        $this->itemId = $itemId;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    public function withMetadata(array $metadata): Message
    {
        $event = clone $this;
        $event->metadata = $metadata;

        return $event;
    }

    public function withAddedMetadata(string $key, $value): Message
    {
        $event = clone $this;
        $event->metadata[$key] = $value;

        return $event;
    }

    public function withVersion(int $version): AggregateChanged
    {
        $event = clone $this;
        $event->version = $version;

        return $event;
    }
}

class TodoItem
{
    /**
     * @AggregateIdentifier
     * @var string
     */
    private $itemId;

    /**
     * TodoItem constructor.
     * @CommandHandler
     * @param PostTodo $command
     */
    public function __construct(PostTodo $command)
    {
        AggregateLifecycle::recordThat(new TodoPosted($command->getItemId()));
    }

    /**
     * @CommandHandler
     * @param UpdateTodo $command
     */
    public function updateTodo(UpdateTodo $command)
    {
        AggregateLifecycle::recordThat(new TodoUpdated($command->getItemId()));
    }

    /**
     * @EventHandler
     * @param TodoPosted $event
     */
    public function onTodoPosted(TodoPosted $event)
    {
        $this->itemId = $event->getItemId();
        echo "Posted: " . $event->getItemId() . "\n";
    }

    /**
     * @EventHandler
     * @param TodoUpdated $event
     */
    public function onTodoUpdated(TodoUpdated $event)
    {
        echo "Updated: " . $event->getItemId() . "\n";
    }
}

class ItemProjector
{
    /**
     * @param TodoPosted $event
     * @EventHandler
     */
    public function onTodoPosted(TodoPosted $event)
    {
        echo "Projecting " . $event->getItemId() . "\n";
    }
}
