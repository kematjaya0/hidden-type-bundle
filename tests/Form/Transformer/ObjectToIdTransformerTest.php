<?php

namespace Kematjaya\HiddenTypeBundle\Tests\Form\Transformer;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Kematjaya\HiddenTypeBundle\DataTransformer\ObjectToIdTransformer;
use Kematjaya\HiddenTypeBundle\Tests\Model\ObjectTest;
use PHPUnit\Framework\TestCase;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectToIdTransformerTest extends TestCase
{
    public function testValidTransformation(): void
    {
        $object = (new ObjectTest())->setName("test");

        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => $object,
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, ObjectTest::class, 'name');

        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals($object, $reversed);
    }

    public function testInvalidValidTransformation(): void
    {
        $object = (new ObjectTest())->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => null,
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, ObjectTest::class, 'name');

        $this->expectException(Exception::class);

        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals(null, $reversed);
    }

    public function testInvalidProperty(): void
    {
        (new ObjectTest())->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => null,
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $this->expectException(Exception::class);
        new ObjectToIdTransformer($registry, ObjectTest::class, 'id');
    }
}
