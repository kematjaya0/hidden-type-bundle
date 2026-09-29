<?php

namespace Kematjaya\HiddenTypeBundle\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectToIdTransformer extends Transformer
{
    const MULTIPLE_SEPARATOR = ',';
    
    /**
     *
     * @var bool
     */
    protected $multiple;
    
    public function __construct(ManagerRegistry $registry, string $class, string $property = 'id', bool $multiple = false)
    {
        $this->multiple = $multiple;
        
        parent::__construct($registry, $class, $property);
    }
    
    public function reverseTransform($id): mixed
    {
        if (null === $id || '' === $id) {
            return $this->multiple ? [] : null;
        }
        
        if ($this->multiple) {
            return $this->reverseTransformMultiple($id);
        }

        $repo     = $this->getRepository();
        $property = $this->getProperty();
        $class    = $this->getClass();

        $result = $repo->findOneBy([
            $property => $id,
        ]);
        
        if ($result === null) {
            throw new TransformationFailedException(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $id));
        }

        return $result;
    }

    public function transform($entity): mixed
    {
        if ($entity === null) {
            return null;
        }
        
        if ($this->multiple) {
            $values = [];
            foreach ($entity as $object) {
                $values[] = $this->getPropertyValue($object);
            }
            
            return implode(self::MULTIPLE_SEPARATOR, array_filter($values, fn ($value) => null !== $value));
        }

        return $this->getPropertyValue($entity);
    }
    
    protected function getPropertyValue($entity)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();
        
        if (! $accessor->isReadable($entity, $property)) {
            return null;
        }

        return $accessor->getValue($entity, $property);
    }
    
    protected function reverseTransformMultiple($ids): array
    {
        $ids = is_array($ids) ? $ids : explode(self::MULTIPLE_SEPARATOR, (string) $ids);
        $ids = array_values(array_unique(array_filter(array_map('trim', $ids), fn ($value) => '' !== $value)));
        if (empty($ids)) {
            return [];
        }
        
        $result = $this->getRepository()->findBy([
            $this->getProperty() => $ids,
        ]);
        
        if (count($result) !== count($ids)) {
            throw new TransformationFailedException(sprintf('Can\'t find all entities of class "%s" with property "%s" in "%s".', $this->getClass(), $this->getProperty(), implode(self::MULTIPLE_SEPARATOR, $ids)));
        }
        
        return $result;
    }

}
