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

use Prooph\Common\Messaging\Command;
use Prooph\EventStore\Exception\InvalidArgumentException;

class AnnotatedCommandTargetResolver implements CommandTargetResolver
{
    /**
     * @param Command $command
     * @return string
     * @throws InvalidArgumentException
     */
    public function resolveTarget(Command $command): string
    {
        $methods = AnnotationUtils::getAnnotatedMethods(get_class($command), TargetAggregateIdentifier::class);

        if (count($methods) > 0) {
            return $methods[0]->invoke($command);
        }

        $properties = AnnotationUtils::getAnnotatedProperties(get_class($command), TargetAggregateIdentifier::class);

        if (count($properties) > 0) {
            $property = $properties[0];
            $property->setAccessible(true);
            return $property->getValue($command);
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid command. It does not identify the target aggregate. ' .
                'Make sure at least one of the properties or methods in the [%s] class contains the ' .
                '@TargetAggregateIdentifier annotation and that it returns a non-null value.',
                get_class($command)
            )
        );
    }
}
