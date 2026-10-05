<?php

namespace Kematjaya\HiddenTypeBundle\DataTransformer;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectToIdTransformer extends Transformer
{
    public function reverseTransform(mixed $id): mixed
    {
        if (null === $id) {
            return null;
        }

        $result = $this->getRepository()->findOneBy([
            $this->getProperty() => $id,
        ]);

        if (null === $result) {
            throw new \Exception(sprintf('Can\'t find entity of class "%s" with property "%s" = "%s".', $this->getClass(), $this->getProperty(), $id));
        }

        return $result;
    }

    public function transform(mixed $entity): mixed
    {
        if (null === $entity) {
            return null;
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        if (!$accessor->isReadable($entity, $this->getProperty())) {
            return null;
        }

        return $accessor->getValue($entity, $this->getProperty());
    }
}
