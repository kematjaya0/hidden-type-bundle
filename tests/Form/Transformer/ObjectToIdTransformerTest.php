<?php

namespace Kematjaya\HiddenTypeBundle\Tests\Form\Transformer;

use Kematjaya\HiddenTypeBundle\DataTransformer\ObjectToIdTransformer;
use Kematjaya\HiddenTypeBundle\Tests\Model\ObjectTest;
use PHPUnit\Framework\TestCase;
use Exception;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ObjectToIdTransformerTest extends TestCase 
{
    public function testValidTransformation()
    {
        $object = (new ObjectTest())->setName("test");
        
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => $object
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
    
    public function testInvalidValidTransformation()
    {
        $object = (new ObjectTest())->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => null
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, ObjectTest::class, 'name');

        $this->expectException(TransformationFailedException::class);
        
        $transformed = $transformer->transform($object);
        $reversed    = $transformer->reverseTransform('test');

        self::assertEquals('test', $transformed);
        self::assertEquals(null, $reversed);
    }
    
    public function testInvalidProperty(): void
    {
        $object = (new ObjectTest())->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => null
        ]);

        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $this->expectException(Exception::class);
        new ObjectToIdTransformer($registry, ObjectTest::class, 'id');
    }

    public function testEmptyValue(): void
    {
        $objectRepository = $this->createMock(ObjectRepository::class);
        $objectRepository->expects($this->never())->method('findOneBy');
        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, ObjectTest::class, 'name');
        self::assertNull($transformer->reverseTransform(''));
        self::assertNull($transformer->reverseTransform(null));
        self::assertNull($transformer->transform(null));
    }

    public function testMultiple(): void
    {
        $a = (new ObjectTest())->setName('a');
        $b = (new ObjectTest())->setName('b');
        $objectRepository = $this->createMock(ObjectRepository::class);
        $objectRepository->expects($this->once())->method('findBy')->with(['name' => ['a', 'b']])->willReturn([$a, $b]);
        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, ObjectTest::class, 'name', true);
        self::assertSame('a,b', $transformer->transform([$a, $b]));
        self::assertSame([$a, $b], $transformer->reverseTransform('a, b,,a'));
        self::assertSame([], $transformer->reverseTransform(''));
    }

    public function testMultipleNotFound(): void
    {
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findBy' => [(new ObjectTest())->setName('a')],
        ]);
        $registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $transformer = new ObjectToIdTransformer($registry, ObjectTest::class, 'name', true);
        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform('a,b');
    }
}
