<?php

namespace Kematjaya\HiddenTypeBundle\DataTransformer;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Exception;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectToIdTransformer extends Transformer
{
    public function reverseTransform(mixed $id) :mixed
    {
        if ($id === null) {
            return null;
        }

        $repo     = $this->getRepository();
        $property = $this->getProperty();
        $class    = $this->getClass();

        $result = $repo->findOneBy([
            $property => $id,
        ]);
        
        if ($result === null) {
            throw new Exception(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $class, $property, $id));
        }

        return $result;
    }

    public function transform(mixed $entity) :mixed
    {
        if ($entity === null) {
            return null;
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        $property = $this->getProperty();
        
        if (! $accessor->isReadable($entity, $property)) {
            return null;
        }

        return $accessor->getValue($entity, $property);
    }

}
