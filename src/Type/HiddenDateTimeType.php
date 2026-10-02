<?php

namespace Kematjaya\HiddenTypeBundle\Type;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class HiddenDateTimeType extends HiddenType implements DataTransformerInterface
{
    public const FORMAT = "Y-m-d H:i:s";

    /**
    * {@inheritdoc}
    */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            fn($data): string => $this->format($data, $options['format']),
            fn($data): \DateTimeInterface => $this->createDateTime($data, $options['input'])
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'input'  => 'datetime',
            'format' => self::FORMAT,
        ]);
        $resolver->setAllowedValues('input', ['datetime', 'datetime_immutable']);
        $resolver->setAllowedTypes('format', ['string']);
    }

    public function transform(mixed $data): mixed
    {
        return $this->format($data, self::FORMAT);
    }

    public function reverseTransform(mixed $data): mixed
    {
        return $this->createDateTime($data, 'datetime');
    }

    /**
     * Format datetime, default to current date when empty
     */
    protected function format(mixed $data, string $format): string
    {
        return ($data instanceof \DateTimeInterface) ? $data->format($format) : date($format);
    }

    /**
     * Create datetime object from submitted value, default to current date when empty
     */
    protected function createDateTime(mixed $data, string $input): \DateTimeInterface
    {
        if (null === $data || '' === $data) {
            $data = 'now';
        }

        if (!is_string($data)) {
            throw new TransformationFailedException('Expected a string.');
        }

        try {
            return 'datetime_immutable' === $input ? new \DateTimeImmutable($data) : new \DateTime($data);
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), 0, $e);
        }
    }

    public function getName(): string
    {
        return 'hidden_datetime';
    }
}
