<?php

namespace Kematjaya\HiddenTypeBundle\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class Transformer implements DataTransformerInterface
{
    public function __construct(
        protected readonly ManagerRegistry $registry,
        protected readonly string $class,
        protected readonly string $property = 'id'
    ) {
        $this->validate();
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->registry->getRepository($this->getClass());
    }

    /**
     * @return class-string
     */
    protected function getClass(): string
    {
        return $this->class;
    }

    protected function getProperty(): string
    {
        return $this->property;
    }

    protected function validate(): void
    {
        $reflectionExtractor = new ReflectionExtractor();
        $propertyInfo        = new PropertyInfoExtractor([$reflectionExtractor]);

        $properties = $propertyInfo->getProperties($this->class) ?? [];

        if (! in_array($this->property, $properties, true)) {
            throw new Exception(sprintf('property %s is missing in class %s', $this->property, $this->class));
        }
    }

}
