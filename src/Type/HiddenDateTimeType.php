<?php

namespace Kematjaya\HiddenTypeBundle\Type;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Hidden field holding a datetime; an empty value means "now".
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class HiddenDateTimeType extends HiddenType implements DataTransformerInterface
{
    public const FORMAT = 'Y-m-d H:i:s';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    public function transform(mixed $data): mixed
    {
        return $data instanceof \DateTimeInterface ? $data->format(self::FORMAT) : date(self::FORMAT);
    }

    public function reverseTransform(mixed $data): mixed
    {
        if (null === $data || '' === $data) {
            $data = 'now';
        }

        if (!\is_string($data)) {
            throw new TransformationFailedException('Expected a string.');
        }

        try {
            return new \DateTime($data);
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), 0, $e);
        }
    }

    public function getName(): string
    {
        return 'hidden_datetime';
    }
}
