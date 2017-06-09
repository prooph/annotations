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
use RuntimeException;

class AggregateManagerTest extends TestCase
{
    protected function setUp()
    {
        AggregateManager::reset();
    }

    public function testShouldNotRegisterExistingAnnotatedAggregate()
    {
        $command = $this->getMockBuilder(Command::class)->getMock();

        $factoryMethod = function () use ($command) {
            return new MockAggregate($command);
        };

        AggregateManager::newInstance($factoryMethod);

        $this->expectException(RuntimeException::class);

        AggregateManager::newInstance($factoryMethod);
    }
}
