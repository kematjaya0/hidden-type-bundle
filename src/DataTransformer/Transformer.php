<?php

namespace Kematjaya\HiddenTypeBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Exception;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class Transformer implements DataTransformerInterface
{
    /**
     *
     * @var ManagerRegistry
     */
    protected $registry;
    
    /**
     *
     * @var string
     */
    protected $class;
    
    /**
     *
     * @var string
     */
    protected $property;
    
    public function __construct(
        ManagerRegistry $registry,
        string $class,
        string $property = 'id'
    ) {
        $this->registry = $registry;
        $this->class    = $class;
        $this->property = $property;
        
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
