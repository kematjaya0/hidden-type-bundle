<?php

namespace Kematjaya\HiddenTypeBundle\Tests\Form\Form;

use Kematjaya\HiddenTypeBundle\Type\HiddenDateTimeType;
use Kematjaya\HiddenTypeBundle\Type\HiddenEntityType;
use Kematjaya\HiddenTypeBundle\Tests\Model\ObjectTest;
use Kematjaya\HiddenTypeBundle\Tests\Model\TestFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class TestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder->add('object', HiddenEntityType::class, [
            'class'    => ObjectTest::class,
            'property' => 'name',
        ])
        ->add('created_at', HiddenDateTimeType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TestFormModel::class,
        ]);
    }
}
