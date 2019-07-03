<?php

/**
 * This file is part of prooph/annotations.
 * (c) 2017-2019 Michiel Rook <mrook@php.net>
 * (c) 2017-2019 prooph Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2017-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\Annotation;

use PHPUnit\Framework\TestCase;
use Prooph\Annotation\AggregateManager;
use Prooph\Annotation\AnnotatedAggregate;
use Prooph\Common\Messaging\Command;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\AggregateChanged;

class AnnotatedAggregateTest extends TestCase
{
    protected function setUp()
    {
        AggregateManager::reset();
    }

    public function testShouldReplayEvents()
    {
        $wrapper = new AnnotatedAggregate();

        $wrapper->replay(new \ArrayIterator([AggregateChanged::occur('aggregateId')->withVersion(5)]));

        static::assertEquals(5, $wrapper->getVersion());
    }

    public function testShouldNotRetrieveAggregateIdWhenAnnotationMissing()
    {
        $wrapper = new AnnotatedAggregate();
        $wrapper->registerAggregate(new class() {
        });

        static::expectException(\RuntimeException::class);
        static::expectExceptionMessageRegExp('/^Missing AggregateIdentifier annotation.*/');

        $wrapper->getAggregateId();
    }

    public function testShouldReturnAggregateType()
    {
        $command = $this->getMockBuilder(Command::class)->getMock();

        $wrapper = AggregateManager::newInstance(function () use ($command) {
            return new MockAggregate($command);
        });

        $aggregateType = $wrapper->aggregateType();

        static::assertInstanceOf(AggregateType::class, $aggregateType);
    }

    public function testShouldRegisterAggregate()
    {
        $command = $this->getMockBuilder(Command::class)->getMock();

        $wrapper = AggregateManager::newInstance(function () use ($command) {
            return new MockAggregate($command);
        });

        static::assertEquals(MockAggregate::AGGREGATE_ID, $wrapper->getAggregateId());
    }

    public function testShouldApplyDelayedEvents()
    {
        $command = $this->getMockBuilder(Command::class)->getMock();

        $wrapper = AggregateManager::newInstance(function () use ($command) {
            return new MockAggregate($command);
        });

        $wrapper->getAggregate()->doSomething($command);

        $this->assertCount(2, $wrapper->popRecordedEvents());
    }

    public function testShouldRecordEvent()
    {
        $command = $this->getMockBuilder(Command::class)->getMock();

        $wrapper = AggregateManager::newInstance(function () use ($command) {
            return new MockAggregate($command);
        });

        $wrapper->getAggregate()->doSomething($command);

        static::assertNotEmpty($wrapper->popRecordedEvents());
    }
}
