<?php

namespace Kematjaya\HiddenTypeBundle\Tests\Form\Form;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Kematjaya\HiddenTypeBundle\Tests\Model\ObjectTest;
use Kematjaya\HiddenTypeBundle\Tests\Model\TestFormModel;
use Kematjaya\HiddenTypeBundle\Type\HiddenDateTimeType;
use Kematjaya\HiddenTypeBundle\Type\HiddenEntityType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class HiddenEntityTypeTest extends TypeTestCase
{
    /**
     *
     * @var ManagerRegistry
     */
    private MockObject $registry;

    private ObjectTest $testObject;

    protected function setUp(): void
    {
        $object = (new ObjectTest())->setName('test');

        // mock any dependencies
        $objectRepository = $this->createConfiguredMock(ObjectRepository::class, [
            'findOneBy' => $object,
        ]);

        $this->registry = $this->createConfiguredMock(ManagerRegistry::class, [
            'getRepository' => $objectRepository,
        ]);

        $this->testObject = $object;

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $type = new HiddenEntityType($this->registry);
        $dateType = new HiddenDateTimeType();
        return [
            new PreloadedExtension([$type, $dateType], []),
        ];
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'object' => 'test', 'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        $data = new TestFormModel();

        $form = $this->factory->create(TestFormType::class, $data);
        $form->submit($formData);

        $testObject = $data->getObject();
        assert($testObject instanceof ObjectTest);

        self::assertTrue($form->isSynchronized());
        self::assertEquals($this->testObject->getName(), $testObject->getName());

        $view     = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }
}
