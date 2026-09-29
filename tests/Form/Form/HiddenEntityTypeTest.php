<?php

namespace Kematjaya\HiddenTypeBundle\Tests\Form\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;
use Kematjaya\HiddenTypeBundle\Tests\Model\TestFormModel;
use Kematjaya\HiddenTypeBundle\Type\HiddenEntityType;
use Kematjaya\HiddenTypeBundle\Type\HiddenDateTimeType;
use Kematjaya\HiddenTypeBundle\Tests\Model\ObjectTest;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class HiddenEntityTypeTest extends TypeTestCase
{
    
    /**
     *
     * @var ManagerRegistry
     */
    private $registry;

    /**
     *
     * @var ObjectTest
     */
    private $testObject;

    protected function setUp(): void
    {
        $object = (new ObjectTest())->setName('test');

        // mock any dependencies
        $objectRepository = $this->createMock(ObjectRepository::class);
        $objectRepository->method('findOneBy')->willReturnCallback(function (array $criteria) use ($object) {
            return $criteria === ['name' => 'test'] ? $object : null;
        });
        $objectRepository->method('findBy')->willReturnCallback(function (array $criteria) use ($object) {
            return in_array('test', $criteria['name'], true) ? [$object] : [];
        });

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
            'object' => 'test', 'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
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

    public function testSubmitUnknownObject(): void
    {
        $data = new TestFormModel();
        $form = $this->factory->create(TestFormType::class, $data);
        $form->submit(['object' => 'unknown', 'created_at' => '2024-01-02 03:04:05']);

        self::assertTrue($form->isSubmitted());
        self::assertFalse($form->isValid());
        self::assertFalse($form->get('object')->isSynchronized());
        $messages = array_map(fn ($error) => $error->getMessage(), iterator_to_array($form->getErrors(true)));
        self::assertContains('The object does not exist.', $messages);
        self::assertNull($data->getObject());
    }

    public function testSubmitEmptyObject(): void
    {
        $data = new TestFormModel();
        $form = $this->factory->create(TestFormType::class, $data);
        $form->submit(['object' => '', 'created_at' => '']);

        self::assertTrue($form->isSynchronized());
        self::assertNull($data->getObject());
        self::assertInstanceOf(\DateTime::class, $data->getCreatedAt());
    }

    public function testViewData(): void
    {
        $data = (new TestFormModel())->setObject($this->testObject)->setCreatedAt(new \DateTime('2024-01-02 03:04:05'));
        $view = $this->factory->create(TestFormType::class, $data)->createView();

        self::assertSame('test', $view->children['object']->vars['value']);
        self::assertSame('2024-01-02 03:04:05', $view->children['created_at']->vars['value']);
    }

    public function testMultiple(): void
    {
        $form = $this->factory->createBuilder()
            ->add('objects', HiddenEntityType::class, ['class' => ObjectTest::class, 'property' => 'name', 'multiple' => true])
            ->getForm();
        $form->submit(['objects' => 'test']);

        self::assertTrue($form->isSynchronized());
        self::assertSame([$this->testObject], $form->get('objects')->getData());
    }

    public function testDateTimeImmutableAndFormat(): void
    {
        $form = $this->factory->createBuilder()
            ->add('date', HiddenDateTimeType::class, ['input' => 'datetime_immutable', 'format' => 'Y-m-d'])
            ->getForm();
        $form->setData(['date' => new \DateTimeImmutable('2024-05-06 07:08:09')]);
        self::assertSame('2024-05-06', $form->createView()->children['date']->vars['value']);

        $form = $this->factory->createBuilder()
            ->add('date', HiddenDateTimeType::class, ['input' => 'datetime_immutable', 'format' => 'Y-m-d'])
            ->getForm();
        $form->submit(['date' => '2024-05-06']);
        self::assertEquals(new \DateTimeImmutable('2024-05-06'), $form->get('date')->getData());
    }

    public function testInvalidDate(): void
    {
        $form = $this->factory->createBuilder()->add('date', HiddenDateTimeType::class)->getForm();
        $form->submit(['date' => 'bukan tanggal']);

        self::assertFalse($form->get('date')->isSynchronized());
    }
}
