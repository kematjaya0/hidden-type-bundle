<?php

namespace Kematjaya\HiddenTypeBundle\Type;

use Kematjaya\HiddenTypeBundle\DataTransformer\ObjectToIdTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class HiddenEntityType extends HiddenType
{
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
    * {@inheritdoc}
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ObjectToIdTransformer(
            $this->registry,
            $options['class'],
            $options['property']
        );
        
        $builder->addModelTransformer($transformer);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['class']);

        $resolver->setDefaults([
            'multiple'        => false,
            'data_class'      => null,
            'invalid_message' => 'The object does not exist.',
            'property'        => 'id',
        ]);

        $resolver->setAllowedTypes('invalid_message', ['null', 'string']);
        $resolver->setAllowedTypes('property', ['null', 'string']);
        $resolver->setAllowedTypes('multiple', ['boolean']);
    }
    
    public function getParent(): string
    {
        return HiddenType::class;
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'hidden_entity';
    }
}
