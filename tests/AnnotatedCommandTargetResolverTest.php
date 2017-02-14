<?php
/**
 * This file is part of the prooph/annotations package.
 * (c) 2017 Michiel Rook <mrook@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\Annotation;

use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Command;
use Prooph\EventStore\Exception\InvalidArgumentException;

class AnnotatedCommandTargetResolverTest extends TestCase
{
    const TARGET_ID = 'targetId';
    
    public function testShouldResolveMethod()
    {
        $resolver = new AnnotatedCommandTargetResolver();
        
        static::assertEquals(static::TARGET_ID, $resolver->resolveTarget(new class extends Command {
            /**
             * @Prooph\Annotation\TargetAggregateIdentifier
             */
            public function getId()
            {
                return AnnotatedCommandTargetResolverTest::TARGET_ID;
            }

            public function payload()
            {
                return [];
            }

            protected function setPayload(array $payload)
            {
            }
        }));
    }
    
    public function testShouldResolveProperty()
    {
        $resolver = new AnnotatedCommandTargetResolver();
        
        static::assertEquals(static::TARGET_ID, $resolver->resolveTarget(new class extends Command {
            /**
             * @Prooph\Annotation\TargetAggregateIdentifier
             */
            public $id = AnnotatedCommandTargetResolverTest::TARGET_ID;

            public function payload()
            {
                return [];
            }

            protected function setPayload(array $payload)
            {
            }
        }));
    }
    
    public function testCommandWithoutAnnotationsShouldNotResolve()
    {
        $resolver = new AnnotatedCommandTargetResolver();
        
        static::expectException(InvalidArgumentException::class);
        
        static::assertEquals(static::TARGET_ID, $resolver->resolveTarget(new class extends Command {
            public function payload()
            {
                return [];
            }

            protected function setPayload(array $payload)
            {
            }
        }));
    }
}
