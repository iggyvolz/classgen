<?php
declare(strict_types=1);

namespace iggyvolz\classgen;

abstract class NamespacedClassGenerator extends ClassGenerator
{
    protected abstract function getNamespace(): string;
    protected function isValid(string $class): bool
    {
        return str_starts_with($class, $this->getNamespace());
    }
}