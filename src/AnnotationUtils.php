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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;

class AnnotationUtils
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * AnnotationUtils constructor.
     * @param Reader $annotationReader
     */
    protected function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @return AnnotationUtils
     * @throws \InvalidArgumentException
     */
    public static function getInstance(): self
    {
        AnnotationRegistry::registerLoader(function($name) {
            if (strpos($name, 'Prooph\\Annotation') === 0) {
                return true;
            }
            return false;
        });

        // @TODO use caching
        $reader = new IndexedReader(new AnnotationReader());
        
        return new self($reader);
    }

    /**
     * @param $className
     * @param $annotation
     * @return \ReflectionMethod[]
     */
    public static function getAnnotatedMethods($className, $annotation): array
    {
        return array_map(function($methodWithAnnotation) {
            return $methodWithAnnotation[0];
        }, self::getAnnotatedMethodsWithAttributes($className, $annotation));
    }

    /**
     * @param $className
     * @param $annotation
     * @return array[\ReflectionMethod,string[]]
     */
    public static function getAnnotatedMethodsWithAttributes($className, $annotation): array
    {
        $methods = [];
        $rc = new \ReflectionClass($className);

        foreach ($rc->getMethods() as $method) {
            $annotationAttributes = self::getInstance()->annotationReader->getMethodAnnotation($method, $annotation);
            if ($annotationAttributes !== null) {
                $methods[] = [$method, (array) $annotationAttributes];
            }
        }

        return $methods;
    }

    /**
     * @param $className
     * @param $annotation
     * @return \ReflectionProperty[]
     */
    public static function getAnnotatedProperties($className, $annotation): array
    {
        $properties = [];
        $rc = new \ReflectionClass($className);

        foreach ($rc->getProperties() as $property) {
            if (self::getInstance()->annotationReader->getPropertyAnnotation($property, $annotation) !== null) {
                $properties[] = $property;
            }
        }
        
        return $properties;
    }
}
