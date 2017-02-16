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

use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Command;
use Prooph\EventSourcing\AggregateChanged;

class AnnotatedAggregateRootTest extends TestCase
{
    public function testShouldNotCallConstructorWhenReconstitutingFromHistory()
    {
        $rm = new \ReflectionMethod(MockAggregate::class, 'reconstituteFromHistory');
        $rm->setAccessible(true);
        $aggregate = $rm->invoke(null, new \ArrayIterator());
        static::assertInstanceOf(MockAggregate::class, $aggregate);
    }
    
    public function testShouldInvokeKnownEventHandler()
    {
        $aggregate = $this->getAggregateRoot();
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(MockAggregate::AGGREGATE_ID);
        
        $aggregate->doSomething(new class extends Command {
            public function payload(): array
            {
                return [];
            }

            protected function setPayload(array $payload): void
            {
            }
        });
    }
    
    public function testShouldNotInvokeUnknownEventHandler()
    {
        $aggregate = $this->getAggregateRoot();
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/^Missing event handler for event .*$/');
        
        $aggregate->doSomethingElse($this->getMockBuilder(AggregateChanged::class)->disableOriginalConstructor()->getMock());
    }

    /**
     * @return MockAggregate
     */
    protected function getAggregateRoot()
    {
        $ref = new \ReflectionClass(MockAggregate::class);
        return $ref->newInstanceWithoutConstructor();
    }
}
