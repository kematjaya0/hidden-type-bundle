<?php

namespace Kematjaya\HiddenTypeBundle\DataTransformer;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class Transformer implements DataTransformerInterface
{
    /**
     * @param class-string $class
     */
    public function __construct(
        protected ManagerRegistry $registry,
        protected string $class,
        protected string $property = 'id',
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
        $propertyInfo = new PropertyInfoExtractor([new ReflectionExtractor()]);
        $properties = $propertyInfo->getProperties($this->class) ?? [];

        if (!\in_array($this->property, $properties, true)) {
            throw new \Exception(sprintf('property %s is missing in class %s', $this->property, $this->class));
        }
    }
}
