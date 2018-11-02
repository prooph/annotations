<?php

/**
 * This file is part of prooph/annotations.
 * (c) 2017-2018 Michiel Rook <mrook@php.net>
 * (c) 2017-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2017-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\Annotation;

use PHPUnit\Framework\TestCase;
use Prooph\Annotation\AggregateManager;
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
