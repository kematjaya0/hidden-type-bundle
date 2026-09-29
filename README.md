# hidden-type-bundle
Branch `6.4`: Symfony 6.4, PHP >= 8.1.

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
3. options
- `HiddenEntityType`
  - `class` (wajib): class entity
  - `property` (default `id`): property yang disimpan di hidden field
  - `multiple` (default `false`): nilai dipisah koma, data berupa array entity
  - `invalid_message`: pesan jika entity tidak ditemukan
- `HiddenDateTimeType` (nilai kosong = waktu sekarang)
  - `input` (default `datetime`): `datetime` atau `datetime_immutable`
  - `format` (default `Y-m-d H:i:s`)

```
$builder
    ->add('tags', HiddenEntityType::class, [
        'class' => \App\Entity\Tag::class,
        'property' => 'slug',
        'multiple' => true,
    ])
    ->add('published_at', HiddenDateTimeType::class, [
        'input' => 'datetime_immutable',
    ]);
```

## Test
```
sh docker/test.sh all
```
