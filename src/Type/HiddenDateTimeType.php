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
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder->addModelTransformer($this);
    }

    /**
    * {@inheritdoc}
    * @return mixed Description
    */
    public function transform(mixed $data):mixed
    {
        return (!is_null($data) and $data instanceof \DateTimeInterface) ? $data->format("Y-m-d H:i:s"): date("Y-m-d H:i:s");
    }

    /**
    * {@inheritdoc}
    * @return mixed Description
    */
    public function reverseTransform(mixed $data):mixed
    {
        try {
            return new \DateTime($data);
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }

    /**
     * 
     * @return string
     */
    public function getName():string
    {
        return 'hidden_datetime';
    }
}
