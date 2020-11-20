<?php

namespace Kematjaya\HiddenTypeBundle\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class HiddenDateTimeType extends HiddenType implements DataTransformerInterface
{
    /**
    * {@inheritdoc}
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    /**
    * {@inheritdoc}
    */
    public function transform($data = null)
    {
        return (!is_null($data) and $data instanceof \DateTimeInterface) ? $data->format("Y-m-d H:i:s"): date("Y-m-d H:i:s");
    }

    /**
    * {@inheritdoc}
    */
    public function reverseTransform($data)
    {
        try {
            return new \DateTime($data);
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }

    public function getName()
    {
        return 'hidden_datetime';
    }
}
