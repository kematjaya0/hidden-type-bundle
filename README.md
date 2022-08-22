# hidden-type-bundle
1. Installation
```
composer require kematjaya/hidden-type-bundle
```
2. usage
```
<?php

namespace App\Form;

...
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Kematjaya\HiddenTypeBundle\Type\HiddenDateTimeType;
use Kematjaya\HiddenTypeBundle\Type\HiddenEntityType;
...

class FooType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('created_at', HiddenDateTimeType::class)
            ->add('store', HiddenEntityType::class, [
                'class' => \App\Entity\Store::class
            ]);

    }
}

```