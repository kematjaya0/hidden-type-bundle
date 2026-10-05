<?php

namespace Kematjaya\HiddenTypeBundle\Type;

use Doctrine\Persistence\ManagerRegistry;
use Kematjaya\HiddenTypeBundle\DataTransformer\ObjectToIdTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class HiddenEntityType extends HiddenType
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ObjectToIdTransformer(
            $this->registry,
            $options['class'],
            $options['property'],
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['class']);

        $resolver->setDefaults([
            'multiple' => false,
            'data_class' => null,
            'invalid_message' => 'The object does not exist.',
            'property' => 'id',
        ]);

        $resolver->setAllowedTypes('invalid_message', ['null', 'string']);
        $resolver->setAllowedTypes('property', ['null', 'string']);
        $resolver->setAllowedTypes('multiple', ['boolean']);
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function getName(): string
    {
        return 'hidden_entity';
    }
}
