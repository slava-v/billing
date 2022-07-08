<?php

declare(strict_types=1);

namespace Billie\Tests\TestTrait;

use ReflectionProperty;

trait TestObjectTrait
{
    /**
     * @param object $object - object instance
     * @param string $propertyName - property to change
     * @param mixed $value - new value
     * @return object Object
     * @throws \ReflectionException
     */
    private function setObjectPropertyValue($object, string $propertyName, $value)
    {
        $property = new ReflectionProperty($object, $propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);

        return $object;
    }
}
